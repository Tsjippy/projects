<?php

namespace TSJIPPY\PROJECTS;

use TSJIPPY;

add_action('tsjippy-frontend-content-post-content-title',  __NAMESPACE__ . '\contentTitle');
/**
 * Adds a description for the project post title input
 * 
 * @param   string  $postType
 */
function contentTitle($postType)
{
    //Property content title
    $class = 'property project';
    if ($postType != 'project') {
        $class .= ' hidden';
    }

    ?>
    <h4 class='<?php echo esc_attr($class); ?>'>
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
/**
 * Save project specific 
 */
function afterPostSave($post, $frontEndPost, $request)
{
    if ($post->post_type != 'project') {
        return;
    }

    foreach(['number','url','manager_user_id','manager_name','manager_tel','manager_email','ministry'] as $key){
        if (!isset($request[$key])) {
            continue;
        }

        if (empty($request[$key])) {
            delete_post_meta($post->ID, "tsjippy_$key");
        } else {
            //Store value
            update_metadata('post', $post->ID, "tsjippy_$key", $request[$key]);
        }
    }
}

//add meta data fields before any other
add_action('tsjippy-frontend-content-post-before-default-options-content',  __NAMESPACE__ . '\afterContent', 10, 2);
/**
 * Shows project inputs
 * 
 * @param object $frontendContend   THe instance
 */
function afterContent($frontendContend)
{
    if (!empty($frontendContend->post) && $frontendContend->post->post_type != 'project') {
        return;
    }

    //Load js
    wp_enqueue_script('tsjippy_project_script');
    $postName   = $frontendContend->postName;

    $managerId    = $frontendContend->getPostMeta('manager_user_id', 0);

    $managerName  = $frontendContend->getPostMeta('manager_name', '');
    $managerTel   = $frontendContend->getPostMeta('manager_tel', '');
    $managerEmail = $frontendContend->getPostMeta('manager_email', '');

    $url          = $frontendContend->getPostMeta('url', '');

    $number       = $frontendContend->getPostMeta('number', '');

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
    <div id="project-attributes" class="property project <?php if ($postName != 'project') echo ' hidden'; ?>">

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
                        <input type='hidden' class='no-reset' class='datalistvalue' name='manager_user_id]' value='<?php echo esc_attr($managerId); ?>'>
                        <input type="text" class='formbuilder' name="manager_name" value="<?php echo esc_attr($managerName); ?>" list='users'>
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager phone number</label></th>
                    <td>
                        <input type="tel" class='formbuilder' name="manager_tel" value="<?php echo esc_attr($managerTel); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager e-mail</label></th>
                    <td>
                        <input type="text" class='formbuilder' name="manager_email" value="<?php echo esc_attr($managerEmail); ?>">
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

        <datalist id="users">
            <?php
            foreach (TSJIPPY\getUserAccounts(false, true) as $user) {
            ?>
                <option data-value='<?php echo esc_attr($user->ID); ?>' value='<?php echo esc_attr($user->display_name); ?>'>
                </option>
            <?php
            }
            ?>
        </datalist>
    </div>
<?php
}


//add meta data fields
add_action('tsjippy-frontend-content-post-after-content',  __NAMESPACE__ . '\addMetas', 10, 2);
/**
 * Adds inputs for the project meta
 * 
 * @param   object  $object 
 */
function addMetas($object)
{
?>
    <tbody class="frontend-form expand-wrapper property project <?php if ($object->postName != 'project') echo ' hidden'; ?>">
        <tr>
            <td>
                <h4>
                    Parent project
                </h4>
            </td>
            <td>

                <button class="button small expand" type='button'>
                    &#9660;
                </button>
            </td>
        </tr>

        <tr>
            <td class="hidden expandable">
                <?php
                TSJIPPY\pageSelect('parent-project', $object->postParent, '', ['project'], false, true);
                ?>
            </td>
        </tr>
    </tbody>

    <tbody class="frontend-form expand-wrapper property project <?php if ($object->postName != 'project') echo ' hidden'; ?>">
        <tr>
            <td>
                <h4>
                    Update warnings
                </h4>
            </td>
            <td>
                <button class="button small expand" type='button'>
                    &#9660;
                </button>
            </td>
        </tr>

        <tr>
            <td class="hidden expandable">
                <input
                    type='checkbox'
                    name='static-content'
                    value='static-content'
                    <?php if (!empty($object->getPostMeta('static_content', ''))) echo 'checked'; ?>>
                Do not send update warnings for this project
            </td>
        </tr>
    </tbody>
<?php
}
