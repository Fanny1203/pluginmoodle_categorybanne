<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library functions for the category banner plugin
 *
 * @package    local_categorybanner
 * @copyright  2025 Service Ecole Media <sem.web@edu.ge.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/classes/rule_manager.php');

/**
 * Extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course object
 * @param context $context The course context
 */
function local_categorybanner_extend_navigation_course($navigation, $course, $context) {
    // This function is needed to ensure the plugin is loaded
    return;
}

/**
 * Insert banner into course pages if applicable
 *
 * @return string HTML content to insert or empty string
 */
function local_categorybanner_before_standard_html_head() {
    global $PAGE;
    
    // Load CSS for banner positioning if we're on a course page
    if ($PAGE->pagelayout === 'course') {
        $PAGE->requires->css('/local/categorybanner/styles.css');
    }
    return '';
}

/**
 * Check if the current page layout is course-related
 *
 * @param string $layout Current page layout
 * @return bool True if layout is course-related
 */
function local_categorybanner_is_course_layout($layout) {
    $course_layouts = array('course', 'incourse', 'report', 'admin', 'coursecategory');
    return in_array($layout, $course_layouts);
}

/**
 * Render banner HTML for a given banner content
 *
 * @param string $content Banner content
 * @return string HTML for the banner
 */
function local_categorybanner_render_banner($content) {
    global $OUTPUT;
    return html_writer::div(
        $OUTPUT->notification(format_text($content, FORMAT_HTML), 'info'),
        'local-categorybanner-notification'
    );
}

/**
 * Insert banner into course pages if applicable
 *
 * @return string HTML content to insert or empty string
 */
function local_categorybanner_before_standard_top_of_body_html() {
    global $COURSE, $PAGE;

    // Only show banner on course-related pages
    if (!local_categorybanner_is_course_layout($PAGE->pagelayout)) {
        return '';
    }

    // Get course category
    $category = \core_course_category::get($COURSE->category, IGNORE_MISSING);
    if (!$category) {
        return '';
    }

    // Get banner content for category
    $banner = \local_categorybanner\rule_manager::get_banner_for_category($category->id);
    if (!empty($banner)) {
        return local_categorybanner_render_banner($banner);
    }

    return '';
}
