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

    ?>
    <h4 class='<?php echo esc_attr($class);?>'>
        Please describe the project
    </h4>
    <?php
}

/**
 * Allow comments
 * 
 * @param   \WP_Post    $post       The new or updated post
 * @param   object      $object     FrontEndContent Instance
 * @param   array       $request    The sanitized request data
 */
add_action('tsjippy-frontend-content-after-post-save',  __NAMESPACE__ . '\afterPostSave', 10, 3);
function afterPostSave($post, $frontEndPost, $request)
{
    if ($post->post_type != 'project') {
        return;
    }

    //manager
    if (isset($request['manager'])) {
        if (empty($request['manager'])) {
            delete_post_meta($post->ID, 'tsjippy_manager');
        } else {
            //Store manager
            update_metadata('post', $post->ID, 'tsjippy_manager', json_encode($request['manager']));
        }
    }

    // number
    if (isset($request['number'])) {
        if (empty($request['number'])) {
            delete_post_meta($post->ID, 'tsjippy_number');
        } else {
            //Store serves
            update_metadata('post', $post->ID, 'tsjippy_number', $request['number']);
        }
    }

    //url
    if (isset($request['url'])) {
        if (empty($request['url'])) {
            delete_post_meta($post->ID, 'tsjippy_url');
        } else {
            //Store serves
            update_metadata('post', $post->ID, 'tsjippy_url', $request['url']);
        }
    }

    // ministry
    if (isset($request['ministry'])) {
        if (empty($request['ministry'])) {
            delete_post_meta($post->ID, 'tsjippy_ministry');
        } else {
            //Store serves
            update_metadata('post', $post->ID, 'tsjippy_ministry', $request['ministry']);
        }
    }
}

//add meta data fields
add_action('tsjippy-frontend-content-post-before-default-options-content',  __NAMESPACE__ . '\afterContent', 10, 2);
function afterContent($frontendContend)
{
    if (!empty($frontendContend->post) && $frontendContend->post->post_type != 'project') {
        return;
    }

    //Load js
    wp_enqueue_script('tsjippy_project_script');
    $postName   = $frontendContend->postName;

    $manager    = $frontendContend->getPostMeta('manager', []);
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

    $url        = $frontendContend->getPostMeta('url', '');

    $number     = $frontendContend->getPostMeta('number', '');

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

    $selectedMinistry = $frontendContend->getPostMeta('ministry', '');

?>
    <div
        id="project-attributes"
        class="property project v
    <?php if ($postName != 'project') echo ' hidden'; ?>">

        <fieldset id="project" class="frontend-form">
            <legend>
                <h4>
                    Project details
                </h4>
            </legend>

            <table class="form-table no-border left">
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
                                    <?php if ($ministry->ID == $selectedMinistry) echo 'selected="selected"'; ?>>
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

        
        <div id="parentpage" class="frontend-form expand-wrapper">
            <h4>
                Parent project
                <button class="button small expand" type='button'>&#9660;</button>
            </h4>

            <div class="hidden expandable">
                <?php
                TSJIPPY\pageSelect('parent-project', $frontendContend->postParent, '', ['project'], false, true);
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
                    <?php if (!empty($frontendContend->getPostMeta('static_content', ''))) echo 'checked'; ?>>
                Do not send update warnings for this project
            </label>
        </div>

        <datalist id="users">
            <?php
            foreach (TSJIPPY\getUserAccounts(false, true) as $user) {
                ?>
                <option data-value='<?php echo esc_attr($user->ID);?>' value='<?php echo esc_attr($user->display_name);?>'>
                </option>
                <?php
            }
            ?>
        </datalist>
    </div>
<?php
}
