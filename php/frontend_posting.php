<?php
namespace SIM\PROJECTS;
use SIM;
    
add_filter('sim_frontend_posting_modals',  __NAMESPACE__.'\frontendPostingModals');
function frontendPostingModals($types){
    $types[]	= 'project';
    return $types;
}

add_action('sim_frontend_post_before_content',  __NAMESPACE__.'\beforeContent');
function beforeContent($frontEndContent){
    $frontEndContent->showCategories('project', 'projects');
}

add_action('sim_frontend_post_content_title',  __NAMESPACE__.'\contentTitle');
function contentTitle($postType){
    //Property content title
    $class = 'property project';
    if($postType != 'project'){
        $class .= ' hidden';
    }
    
    echo "<h4 class='$class'>";
        echo 'Please describe the project';
    echo "</h4>";
}

add_action('sim_after_post_save',  __NAMESPACE__.'\afterPostSave', 10, 2);
function afterPostSave($post, $frontEndPost){
    if($post->post_type != 'project'){
        return;
    }
    
    //store categories
    $frontEndPost->storeCustomCategories($post, 'projects');
    
    //parent
    if(isset($_POST['parent-project'])){
        if(empty($_POST['parent-project'])){
            $parent = 0;
        }else{
            $parent = $_POST['parent-project'];
        }

        wp_update_post(
            array(
                'ID'            => $post->ID,
                'post_parent'   => $parent
            )
        );
    }

    //manager
    if(isset($_POST['manager'])){
        if(empty($_POST['manager'])){
            delete_post_meta($post->ID, 'manager');
        }else{
            //Store manager
            update_metadata( 'post', $post->ID, 'manager', json_encode($_POST['manager']));
        }
    }

    // number
    if(isset($_POST['number'])){
        if(empty($_POST['number'])){
            delete_post_meta($post->ID, 'number');
        }else{
            //Store serves
            update_metadata( 'post', $post->ID, 'number', $_POST['number']);
        }
    }
    
    //url
    if(isset($_POST['url'])){
        if(empty($_POST['url'])){
            delete_post_meta($post->ID, 'url');
        }else{
            //Store serves
            update_metadata( 'post', $post->ID, 'url', $_POST['url']);
        }
    }

    // ministry
    if(isset($_POST['ministry'])){
        if(empty($_POST['ministry'])){
            delete_post_meta($post->ID, 'ministry');
        }else{
            //Store serves
            update_metadata( 'post', $post->ID, 'ministry', $_POST['ministry']);
        }
    }
}

//add meta data fields
add_action('sim_frontend_post_after_content',  __NAMESPACE__.'\afterContent', 10, 2);
function afterContent($frontendContend){
    if(!empty($frontendContend->post) && $frontendContend->post->post_type != 'project'){
        return;
    }

    //Load js
    wp_enqueue_script('sim_project_script');

    $postId     = $frontendContend->postId;
    $postName   = $frontendContend->postName;
    
    $manager    = (array) $frontendContend->getPostMeta('manager');
    $managerId  = '';
    if(isset($manager['user-id'])){
        $managerId  = $manager['user-id'];
    }

    $managerName  = '';
    if(isset($manager['name'])){
        $managerName  = $manager['name'];
    }

    $managerTel  = '';
    if(isset($manager['tel'])){
        $managerTel  = $manager['tel'];
    }

    $managerEmail  = '';
    if(isset($manager['email'])){
        $managerEmail  = $manager['email'];
    }

    $url        = $frontendContend->getPostMeta('url');

    $number     = $frontendContend->getPostMeta('number');

    //Get all pages describing a ministry
	$ministries = get_posts([
		'post_type'			=> 'location',
		'posts_per_page'	=> -1,
		'post_status'		=> 'publish',
        'orderby'           => 'title',
        'order'             => 'ASC',
		'tax_query' => array(
            array(
                'taxonomy'	=> 'locations',
				'field' => 'term_id',
				'terms' => get_term_by('name', 'Ministries', 'locations')->term_id
            )
        )
	]);

    $selectedMinistry = $frontendContend->getPostMeta('ministry');
    
    ?>
    <style>
        .form-table, .form-table th, .form-table, td{
            border: none;
        }
        .form-table{
            text-align: left;
        }
    </style>
    <div id="project-attributes" class="property project<?php if($postName != 'project'){echo ' hidden';} ?>">
        <div id="parentpage" class="frontend-form">
            <h4>Select a parent project</h4>
            <?php
            echo SIM\pageSelect('parent-project', $frontendContend->postParent, '', ['project'], false);
            ?>
        </div>
        <div class="frontend-form">
            <h4>Update warnings</h4>
            <label>
                <input type='checkbox' name='static-content' value='static-content' <?php if(!empty($frontendContend->getPostMeta('static_content'))){echo 'checked';}?>>
                Do not send update warnings for this project
            </label>
        </div>

        <datalist id="users">
            <?php
            foreach(SIM\getUserAccounts(false,true,true) as $user){
                echo "<option data-value='{$user->ID}' value='{$user->display_name}'></option>";
            }
            ?>
        </datalist>

        <fieldset id="project" class="frontend-form">
            <legend>
                <h4>Project details</h4>
            </legend>
        
            <table class="form-table">
                <tr>
                    <th><label for="number">Project Number</label></th>
                    <td>
                        <input type='number' name='number' value='<?php echo $number; ?>'>
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager name</label></th>
                    <td>
                        <input type='hidden' class='no-reset' class='datalistvalue' name='manager[user-id]' value='<?php echo $managerId; ?>'>
                        <input type="text" class='formbuilder' name="manager[name]" value="<?php echo $managerName; ?>" list='users'>
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager phone number</label></th>
                    <td>
                        <input type="tel" class='formbuilder' name="manager[tel]" value="<?php echo $managerTel; ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Manager e-mail</label></th>
                    <td>
                        <input type="text" class='formbuilder' name="manager[email]" value="<?php echo $managerEmail; ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="url">Project Url</label></th>
                    <td>
                        <input type='url' class='formbuilder' name='url' value='<?php echo $url; ?>'>
                    </td>
                </tr>
                <tr>
                    <th><label for="ministry">Ministry this project is connected to</label></th>
                    <td>
                        <select name='ministry'>
                            <option value=''>---</option>
                            <?php
                            foreach($ministries as $ministry){
                                $selected   = '';
                                if($ministry->ID == $selectedMinistry){
                                    $selected   = 'selected="selected"';
                                }
                                echo "<option value='$ministry->ID' $selected>$ministry->post_title</option>";
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