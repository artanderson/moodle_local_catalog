# Course Catalog - Moodle Local Plugin

A Moodle local plugin that provides an enhanced course catalog experience with category browsing, course search, and a modern card-based UI.

## Features

- **Category Overview**: Displays all course categories with up to 3 course previews per category in a responsive card grid layout
- **Category Drill-down**: View all courses within a specific category with full course details and pagination
- **Course Search**: Search across all courses with results showing course name, summary, and category
- **Category Quick-Select**: Dropdown menu for fast navigation between categories
- **Navigation Integration**: Adds a "Catalog" link to the primary navigation bar
- **Permission-Aware**: Respects Moodle's visibility settings—users only see categories and courses they have access to
- **Pagination**: Configurable number of courses per page when browsing categories or search results

## Requirements

- Moodle 4.5 or higher (version 2025092600+)

## Installation

1. Download or clone this repository into your Moodle installation's `local/` directory:
   ```
   cd /path/to/moodle/local
   git clone <repository-url> catalog
   ```

2. Visit **Site administration → Notifications** to complete the installation

3. Configure the plugin at **Site administration → Plugins → Local plugins → Catalog settings**

## Configuration

Navigate to **Site administration → Plugins → Local plugins → Catalog settings** to configure:

| Setting | Description | Default |
|---------|-------------|---------|
| **Show Catalog in nav** | Add the Catalog link to the main navigation bar | Enabled |
| **Courses per page** | Maximum number of courses to display per page when viewing a category or search results | 5 |

## Usage

### Browsing the Catalog

1. Click **Catalog** in the main navigation (if enabled)
2. The main page displays all visible categories with course previews
3. Click a category name or "See all X courses" to view all courses in that category
4. Use the category dropdown to quickly jump to a different category

### Searching Courses

1. Use the search bar at the top of the catalog page
2. Enter a search term and press Enter or click Search
3. Results display with course name, summary, and category information

## File Structure

```
local/catalog/
├── classes/
│   ├── local/
│   │   └── hook_callbacks.php    # Navigation hook for adding Catalog link
│   └── output/
│       ├── main.php              # Main renderable class
│       └── renderer.php          # Plugin renderer
├── db/
│   └── hooks.php                 # Hook definitions
├── lang/
│   └── en/
│       └── local_catalog.php     # Language strings
├── templates/
│   ├── card.mustache             # Course card template
│   ├── category.mustache         # Category course grid
│   ├── course.mustache           # Course detail row
│   ├── error.mustache            # Error display
│   ├── main.mustache             # Main page layout
│   ├── pagination.mustache       # Pagination controls
│   └── search.mustache           # Search bar with category dropdown
├── index.php                     # Main catalog page
├── search.php                    # Search results page
├── settings.php                  # Admin settings
├── styles.css                    # Custom styles
└── version.php                   # Plugin version info
```

## License

This plugin is licensed under the [GNU GPL v3](LICENSE).

