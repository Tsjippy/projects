<?php

namespace TSJIPPY\PROJECTS;

use TSJIPPY;

add_action('tsjippy-frontend-content-post-content-title',  __NAMESPACE__ . '\contentTitle');
function contentTitle($postType)
{
    //Property content title
    $class = 'property project';
    if ($postType != 'project') {
        $class .= ' hidden';
    }

    echo "<h4 class='$class'>";
    echo 'Please describe the project';
    echo "</h4>";
}

add_action('tsjippy-frontend-content-after-post-save',  __NAMESPACE__ . '\afterPostSave', 10, 2);
function afterPostSave($post, $frontEndPost)
{
    if ($post->post_type != 'project') {
        return;
    }

    //manager
    if (isset($_POST['manager'])) {
        if (empty($_POST['manager'])) {
            delete_post_meta($post->ID, 'tsjippy_manager');
        } else {
            //Store manager
            update_metadata('post', $post->ID, 'tsjippy_manager', json_encode(TSJIPPY\sanitize($_POST['manager'])));
        }
    }

    // number
    if (isset($_POST['number'])) {
        if (empty($_POST['number'])) {
            delete_post_meta($post->ID, 'tsjippy_number');
        } else {
            //Store serves
            update_metadata('post', $post->ID, 'tsjippy_number', TSJIPPY\sanitize($_POST['number']));
        }
    }

    //url
    if (isset($_POST['url'])) {
        if (empty($_POST['url'])) {
            delete_post_meta($post->ID, 'tsjippy_url');
        } else {
            //Store serves
            update_metadata('post', $post->ID, 'tsjippy_url', TSJIPPY\sanitize($_POST['url'], 'url'));
        }
    }

    // ministry
    if (isset($_POST['ministry'])) {
        if (empty($_POST['ministry'])) {
            delete_post_meta($post->ID, 'tsjippy_ministry');
        } else {
            //Store serves
            update_metadata('post', $post->ID, 'tsjippy_ministry', TSJIPPY\sanitize($_POST['ministry']));
        }
    }
}

//add meta data fields
add_action('tsjippy-frontend-content-post-after-content',  __NAMESPACE__ . '\afterContent', 10, 2);
function afterContent($frontendContend)
{
    if (!empty($frontendContend->post) && $frontendContend->post->post_type != 'project') {
        return;
    }

    //Load js
    wp_enqueue_script('tsjippy_project_script');
    $postName   = $frontendContend->postName;

    $manager    = (array) $frontendContend->getPostMeta('manager');
    $managerId  = '';
    if (isset($manager['user-id'])) {
        $managerId  = $manager['user-id'];
    }

    $managerName  = '';
    if (isset($manager['name'])) {
        $managerName  = $manager['name'];
    }

    $managerTel  = '';
    if (isset($manager['tel'])) {
        $managerTel  = $manager['tel'];
    }

    $managerEmail  = '';
    if (isset($manager['email'])) {
        $managerEmail  = $manager['email'];
    }

    $url        = $frontendContend->getPostMeta('url');

    $number     = $frontendContend->getPostMeta('number');

    //Get all pages describing a ministry
    $ministries = get_posts([
        'post_type'         => 'location',
        'posts_per_page'    => -1,
        'post_status'       => 'publish',
        'orderby'           => 'title',
        'order'             => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy'  => 'locations',
                'field'     => 'term_id',
                'terms'     => get_term_by('name', 'Ministries', 'locations')->term_id
            )
        )
    ]);

    $selectedMinistry = $frontendContend->getPostMeta('ministry');

?>
    <div
        id="project-attributes"
        class="property project v
    <?php if ($postName != 'project') {
        echo ' hidden';
    } ?>">
        <div id="parentpage" class="frontend-form expand-wrapper">
            <h4>
                Select a parent project
                <button class="button small expand" type='button'>&#9660;</button>
            </h4>

            <div class="hidden expandable">
                <?php
                echo TSJIPPY\pageSelect('parent-project', $frontendContend->postParent, '', ['project'], false);
                ?>
            </div>
        </div>

        <div class="frontend-form expand-wrapper">
            <h4>
                Update warnings
                <button class="button small expand" type='button'>&#9660;</button>
            </h4>
            <label class="hidden expandable">
                <input
                    type='checkbox'
                    name='static-content'
                    value='static-content'
                    <?php if (!empty($frontendContend->getPostMeta('static_content'))) {
                        echo 'checked';
                    } ?>>
                Do not send update warnings for this project
            </label>
        </div>

        <datalist id="users">
            <?php
            foreach (TSJIPPY\getUserAccounts(false, true) as $user) {
                echo "<option data-value='{$user->ID}' value='{$user->display_name}'></option>";
            }
            ?>
        </datalist>

        <fieldset id="project" class="frontend-form expand-wrapper">
            <legend>
                <h4>
                    Project details
                    <button class="button small expand" type='button'>&#9660;</button>
                </h4>
            </legend>

            <table class="form-table no-border left hidden expandable">
                <tr>
                    <th><label for="number">Project Number</label></th>
                    <td>
                        <input type='number' name='number' value='<?php echo esc_attr($number); ?>'>
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager name</label></th>
                    <td>
                        <input type='hidden' class='no-reset' class='datalistvalue' name='manager[user-id]' value='<?php echo esc_attr($managerId); ?>'>
                        <input type="text" class='formbuilder' name="manager[name]" value="<?php echo esc_attr($managerName); ?>" list='users'>
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager phone number</label></th>
                    <td>
                        <input type="tel" class='formbuilder' name="manager[tel]" value="<?php echo esc_attr($managerTel); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager e-mail</label></th>
                    <td>
                        <input type="text" class='formbuilder' name="manager[email]" value="<?php echo esc_attr($managerEmail); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="url">Project Url</label></th>
                    <td>
                        <input type='url' class='formbuilder' name='url' value='<?php echo esc_url($url); ?>'>
                    </td>
                </tr>
                <tr>
                    <th><label for="ministry">Ministry this project is connected to</label></th>
                    <td>
                        <select name='ministry'>
                            <option value=''>---</option>
                            <?php
                            foreach ($ministries as $ministry) {
                            ?>
                                <option
                                    value='<?php echo esc_attr($ministry->ID); ?>'
                                    <?php if ($ministry->ID == $selectedMinistry) {
                                        echo 'selected="selected"';
                                    } ?>>
                                    <?php echo esc_html($ministry->post_title); ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
<?php
}
