<?php

namespace local_catalog\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

class main implements renderable, templatable {
    private $data;

    public function __construct(stdClass $data) {
        $this->data = $data;
    }

    public function export_for_template(renderer_base $output) {
        if(isset($this->data->haserror) && $this->data->haserror) {
            return $this->data;
        }
        $notfoundmessage = "No courses available to display";
        $categories = $this->get_categories_for_user();
        $this->data->search = $this->format_search_for_template($categories);

        $page = (int) $this->data->page ?: 1;
        $limit = get_config('local_catalog', 'coursesperpage') ?? 5;
        $offset = ($page - 1) * $limit;
        $numresults = 0;

        if(isset($this->data->ismain) && $this->data->ismain) {
            $categoryname = $this->data->category->name;
            $notfoundmessage = "No courses found for category '$categoryname'";

            $this->data->heading = $categoryname;
            $this->data->description = $this->data->category->description;

            $numresults = $this->data->category->coursecount;
            if($numresults) {
                $courseids = $this->data->category->get_courses(['idonly'=>true, 'limit' => $limit, 'offset'=>$offset]);
                $this->data->courses = $this->get_courses_for_display($courseids);
            }
        }
        elseif(isset($this->data->query)) {
            $query = $this->data->query;
            $notfoundmessage = "No courses found for search '$query'";

            $courseids = \core_course_category::search_courses(['search'=>$query], ['idonly'=>true, 'limit' => $limit, 'offset'=>$offset]);
            $numresults = \core_course_category::search_courses_count(['search'=>$query]);
            if($numresults) {
                $this->data->heading = "$numresults result" . ($numresults > 1 ? 's' : '') . " for '$query'";
                $this->data->courses = $this->get_courses_for_display($courseids, true);
                $this->data->ismain = true;
            }
        }
        else {
            $pageresults = $this->get_categories_for_page($categories);
            $numresults = $pageresults['numresults'];
            $this->data->categories = $pageresults['categories'];
        }
                
        if($numresults > $limit) {
            $this->get_pagination_params($page, $limit, $numresults);
        }
        elseif($numresults == 0) {
            $this->data->notfound = true;
            $this->data->notfoundmessage = $notfoundmessage;
            $this->data->notfoundimg = $output->image_url('not_found', 'local_catalog');
        }
        
        return $this->data;
    }

    private function get_page_url($page) {
        $query = $this->data->query ?? null;
        $categoryid = $this->data->category?->id ?? null;
        
        $params = [
            'query'=> $query == null ? null : "q=$query",
            'categoryid'=> $categoryid == null ? null : "categoryid=$categoryid",
            'page'=> $page == 1 ? null : "page=$page"
        ];
        $urlparams = [];
        foreach ($params as $param) {
            if($param != null) {
                $urlparams[] = $param;
            }
        }
        $path = $query == null ? 'index.php' : 'search.php';
        $paramstring = implode("&", $urlparams);
        return "/local/catalog/$path?$paramstring";
    }

    private function get_pagination_params($page, $limit, $numresults) {
        $numpages = ceil($numresults / $limit);
        
        $this->data->haspages = true;
        $this->data->label = 'Page';
        $this->data->pagesize = $limit;
        
        $prevpage = max(1, $page - 1);
        $this->data->previous = (object) [
            'page'=> $prevpage, 
            'disabled'=> $page == 1, 
            'url'=> $this->get_page_url($prevpage)
        ];

        $nextpage = min($numpages, $page + 1);
        $this->data->next = (object) [
            'page'=> $page == $nextpage,
            'disabled'=>$page == $numpages,
            'url'=> $this->get_page_url($nextpage)
        ];
        
        $this->data->pages = [];
        for ($i = 1; $i <= $numpages; $i++) {
            $this->data->pages[] = (object) ['page'=> $i,'url'=> $this->get_page_url($i), 'active'=> ($i == $page)];
        }
    }

    private function get_categories_for_user() {
        $all_categories = \core_course_category::get_all(['returnhidden'=>true]);
        $categories = [];

        foreach ($all_categories as $category) {
            if (\core_course_category::can_view_category($category)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    private function get_categories_for_page(array $categories) {
        $pagecategories = [];
        $numresults = 0;
        foreach ($categories as $category) {
            if (\core_course_category::can_view_category($category)) {
                $cat = new stdClass();
                $cat->id = $category->id;
                $cat->name = $category->name;
                $cat->description = $category->description;
                $cat->coursecount = $category->coursecount > 3 ? $category->coursecount : 0;
                $cat->courses = $this->get_courses_for_display($category->get_courses([ 'limit' => 3, 'idonly' => true ]));
                
                $numresults += $category->coursecount;
                
                if(!empty($cat->courses)) {
                    $pagecategories[] = $cat;
                }
            }
        }
        return ['categories'=> $pagecategories, 'numresults'=> $numresults];
    }

    private function get_courses_for_display(array $courseids, $hascategory = false) {
        global $OUTPUT, $DB;  
        
        $courses = [];

        foreach ($courseids as $key=>$courseid) {
            $courseids[$key] = (int) $courseid;
            
            $course = get_course($courseid);
            $course->viewurl = new \moodle_url('/course/view.php', ['id'=>$course->id]);
            $courseimage = \core_course\external\course_summary_exporter::get_course_image($course);
            $course->courseimage = $courseimage ?: $OUTPUT->get_generated_image_for_id($course->id);
            if($hascategory) {
                $course->hascategory = $hascategory;
                $course->categoryname = $DB->get_record('course_categories', ['id'=> $course->category])->name;
            }
            $courses[] = $course;
        }

        return $courses;
    }

    private function format_search_for_template(array $categories) {
        $search = new stdClass();
        $search->menu = new stdClass();
        $search->menu->dropdownid = 'categorySelect';
        $search->menu->buttonid = 'categorySelectBtn';
        $search->menu->buttontext = 'Categories';
        $search->menu->buttonclasses = 'btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center remove-icon rounded-1 catalog-category-select-btn';
        $search->menu->dialogclasses = 'dropdown-menu rounded-1';
        $search->menu->categories = $categories;

        $search->form = new stdClass();
        $search->form->action = '/local/catalog/search.php';
        $search->form->searchstring = 'Search courses';
        $search->form->inputname = 'q';
        $search->form->btnclass = 'btn-primary';
        
        return $search;
    }
}
