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
 * Code for rendering module-specific HTML
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Used for creting navigation (page numbers and previous/forward links)
 *
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mythtranscode_pagination {
    public $num_pages;
    public $current_page;
    public $base_data;
    public $start_index;
    public $end_index;

    /**
     * Initialise and do the calculations required for outputting the buttons
     *
     * @param int $num_pages --- total number of pages
     * @param int $current_page --- current page (1-indexed)
     * @param array $base_data --- data to send with every link (e.g. id)
     */
    public function __construct($num_pages, $current_page, array $base_data) {
            $this->num_pages = $num_pages;
            $this->current_page = $current_page;
            $this->base_data = $base_data;

            // Show a maximum of 5 page buttons before and after the current page.
            $this->start_index = max(1, $current_page - 5);
            $this->end_index = min($num_pages, $current_page + 5);
    }

    /**
     * Render the pagination buttons
     *
     * @return string --- the rendered html
     */
    public function render() {
        global $CFG;

        // Store all of the navigation items in here.
        $navigation = array();

        // If we're not on the first page, output a previous button, otherwise
        // output an inactive grey one.
        if ($this->current_page != 1) {
            $link_data = $this->base_data;
            $link_data['start'] = ($this->current_page-2) *
                $CFG->mod_mythtranscode_num_results;
            array_push($navigation, html_writer::link(new moodle_url('choose.php',
                $link_data), 'Previous', array('class'=>'mythtranscode_previous')));
        } else {
            array_push($navigation, html_writer::tag('a', 'Previous',
                array('class'=>'mythtranscode_inactive mythtranscode_previous')));
        }

        // Output the pagination buttons.
        foreach (range($this->start_index, $this->end_index) as $i) {
            $link_data = $this->base_data;
            $link_data['start'] = ($i-1) * $CFG->mod_mythtranscode_num_results;

            $css_attributes = array();
            if ($i == $this->current_page) {
                $css_attributes['class'] = 'current';
            }

            array_push($navigation, html_writer::link(new moodle_url('choose.php',
                $link_data), $i, $css_attributes));
        }

        // If we're not on the last page, output a next button, otherwise output
        // a grey one.
        if ($this->current_page != $this->num_pages) {
            $link_data = $this->base_data;
            $link_data['start'] = ($this->current_page) *
                $CFG->mod_mythtranscode_num_results;
            array_push($navigation, html_writer::link(new moodle_url('choose.php',
                $link_data), 'Next', array('class'=>'mythtranscode_next')));
        } else {
            array_push($navigation, html_writer::tag('a', 'Next',
                array('class'=>'mythtranscode_inactive mythtranscode_next')));
        }

        // Return the navigation, inside a list.
        return html_writer::tag('div', html_writer::alist($navigation,
            array('class'=>'mythtranscode_pagination')),
        array('class'=>'mythtranscode_pagination_wrapper'));
    }
}

/**
 * Creates a search form, with query as the only user-inputtable field
 *
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mythtranscode_search_form {
    public $basedata;
    public $query;

    /**
     * Construction function.
     *
     * @param $basedata --- any data that should be passed on when the form
     * submits
     * @param $query --- an existing query to prefill the search box with
     */
    public function __construct($basedata, $query) {
        $this->basedata = $basedata;
        $this->query = $query;
    }

    /**
     * Renders form and returns HTML
     * 
     * @return string
     */
    public function render() {
        $form = '<form id="mythtranscode_search_form">';

        // Add a search box, with a prefilled query if we have one.
        $value = empty($this->query) ? '' : "value='{$this->query}'";
        $form .= "<input type='search' id='mythtranscode_searchbox'
                    placeholder='Search...' {$value} name=query>";

        // Add a hidden field containing the course id. This is needed
        // as the data is being posted to the current page, which requires a
        // course id.
        $form .= "<input type='hidden' name='course' value='{$this->basedata['course']}'>";

        // Add a search button.
        $form .= '<button type="submit">Search</button></form>';

        return $form;
    }
}

/**
 * Class representing a video recording
 * Just used for generating URLS for rendering onscreen
 *
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mythtranscode_video implements renderable {
    public $basename;
    public $urls;
    public $title;
    public $date;
    public $channel;

    /**
     * Construction function.
     *
     * @param $basename --- basename of the vidoe
     * @param $title --- title of video
     * @param $date --- date of recording
     * @param $channel --- channel that broadcast recording
     * @param $param_string --- non-video parameters to include in the URL
     */
    public function __construct($basename, $param_string, $title, $date, $channel) {
        $this->urls = array();
        $this->title = $title;
        $this->date = $date;
        $this->channel = $channel;

        // For each video format, generate and store the video url for it.
        foreach (mythtranscode_get_formats() as $format) {
            $this->urls[$format] = mythtranscode_create_video_url($basename, $format, $param_string);
        }
    }
}

/**
 * Class representing a table of search results (of recordings)
 * Used for rendering such data onscreen
 *
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mythtranscode_results_table implements renderable {
    public $results;
    public $base_data;
    public $data;

    /**
     * Initialise a new results table.
     *
     * @param array $data --- the actual results data
     * @param array $base_data --- any data that should be passed on to new
     * urls (e.g., can be used for passing on the id parameter)
     */
    public function __construct(array $data, array $base_data) {
        $this->results = $data;
        $this->base_data = $base_data;

        $data = array();
        foreach ($this->results as $result) {
            $datum = array();

            // Only keep data where the fields should be shown.
            foreach (mythtranscode_get_keys() as $key) {
                $datum[$key] = $result[$key];
            }

            // Render the html for the row.
            $row = $this->mythtranscode_render_row($datum, $result, $this->base_data);
            array_push($data, $row);
        }
        $this->data = $data;
    }

    /**
     * Render the html for a table row
     *
     * @param array $data_row --- an associative array containing the data for
     * the row
     * @param array $original_row --- an associative array containing the
     * original data
     * @param array $base_data --- any data that should be passed on to new
     * urls (eg., can be used for passing on the id parameter)
     *
     * @return html_table_row
     */
    private static function mythtranscode_render_row(array $data_row, array $original_row, array $base_data) {
        $row = new html_table_row();

        $cells = array();
        foreach ($data_row as $key => $cell) {
            // If we need to, make the text in the cell bold
            if (in_array($key, mythtranscode_get_bold_fields())) {
                $text = html_writer::tag('b', $cell);
            } else if ($key === 'progstart') {
                // Format the date
                $text = mythtranscode_format_date($cell);
            } else {
                $text = $cell;
            }

            // If the cell should contain a link, generate it.
            if (in_array($key, mythtranscode_get_link_keys())) {
                $data = $base_data;
                $link_options = array('class'=>'mythtranscode_link');
                $data['basename'] = $original_row['basename'];

                $contents = html_writer::link(new moodle_url('chosen.php', $data), $text, $link_options);
            } else {
                // Otherwise just put text in the cell.
                $contents = $text;
            }

            array_push($cells, $contents);
        }

        $row->cells = $cells;
        return $row;
    }
}

/**
 * Class for rendering results tables and videos
 *
 * @see plugin_renderer_base
 *
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_mythtranscode_renderer extends plugin_renderer_base {
    /**
     * Render a results table (of recordsings)
     *
     * @param mythtranscode_results_table $results_table
     *
     * @return string --- the html output
     */
    protected function render_mythtranscode_results_table(mythtranscode_results_table $results_table) {
        $table = new html_table();
        $table->attributes = array('class' => 'mythtranscode_results_table');

        // Get human-readable versions of each of the field names.
        $human_keys = array();
        foreach (mythtranscode_get_keys() as $key) {
            array_push($human_keys, get_string('key_' . $key, 'mythtranscode'));
        }
        $table->head = $human_keys;

        // Populate the table with data.
        $table->data = $results_table->data;

        $out = html_writer::table($table);

        return $this->output->container($out, 'results');
    }

    /**
     * Render a video recording
     *
     * @param mythtranscode_video $video
     *
     * @return string --- the html output
     */
    protected function render_mythtranscode_video(mythtranscode_video $video) {
        $sources = array();
        $downloads = array();

        global $CFG;

        // For each format the video is stored in.
        foreach (mythtranscode_get_formats() as $format) {
            // Generate an HTML5 video tag.
            $tag = html_writer::empty_tag('source', array('src' => $video->urls[$format],
                'type' => "video/{$format}"));
            // And a link to download the video.
            $download = html_writer::link(new moodle_url($video->urls[$format]),
                get_string('format_'.$format, 'mythtranscode').' ');

            array_push($sources, $tag);
            array_push($downloads, $download);
        }

        // Combine all of the HTML5 video tags.
        $player = html_writer::tag('video', implode($sources), array('controls' => true,
            'width' => '60%', 'autoplay' => true));
        $out = $this->output->container($player, 'video');

        // Format the date
        $date = mythtranscode_format_date($video->date);

        // Output some video metadata
        $out .= html_writer::tag('b', $video->title . ',');
        $out .= " {$video->channel}, {$date}";

        // Combine all of the download links, if download links have been
        // configured to be shown
        if ($CFG->mod_mythtranscode_downloads == '1') {
            $download_links = html_writer::tag('b', 'Download: ') . implode($downloads);

            $out .= $this->output->container($download_links, 'mythtranscode_video_links');
        }

        $out .= html_writer::link(new moodle_url('http://www.era.org.uk/doc/ERA%20PLUS%20Schedule%202013.pdf'), get_string('copyright_string', 'mythtranscode'), array('class'=>'mythtranscode_copyright'));

        return $out;
    }
}
