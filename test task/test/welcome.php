<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


require_once 'config.php';
        
    function categoryTree($parent_id = 0, $sub_mark = ''){
        global $link;
        $query = $link->query("SELECT * FROM categories WHERE parent_id = $parent_id ORDER BY name ASC");
       
        if($query->num_rows > 0){
            while($row = $query->fetch_assoc()){
                echo '<option value="'.$row['id'].'">'.$sub_mark.$row['name'].'</option>';
                categoryTree($row['id'], $sub_mark.'---');
            }
        }
    } 


    function categoryTreeContent($parent_id = 0, $sub_mark = ''){
        global $link;
        $query = $link->query("SELECT * FROM categories WHERE parent_id = $parent_id ORDER BY name ASC");
       
        if($query->num_rows > 0){
            while($row = $query->fetch_assoc()){
                echo '<div class="hidden" id="'.$row['id'].'"> <h3>'.$sub_mark.$row['name'].'</h3>';
                echo '<p>'.$row['content'].'</p> </div>';
                categoryTreeContent($row['id'], $sub_mark.''.$row['name'].' > ');
            }
        }
    } 
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/style.css">
    <title>Welcome</title>
</head>
<body>

    <div class="row">
        <div class="header">
            <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome.</h1>
        </div>

    <div>
        <select id="selectContent" name="category" onchange="contentReveal()">
        <option value="-1">Select category</option>
            <?php categoryTree(); ?>
        </select>
        
    </div>    

        <?php categoryTreeContent(); ?>


        <?php
        
        $parent_id = 0;
        $name_err = $parent_id_err = "";
        $name = $content = "";

        $parent_id_delete = -1;
        $parent_id_delete_err = "";

        $parent_id_update = 0;
        $upd_name = $upd_content = $id_update = "";
        $parent_id_update_err = $upd_name_err = $upd_content_err = $id_update_err = "";


        if($link === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        

        if($_SERVER["REQUEST_METHOD"] == "POST"){


            if(isset($_POST['form'])){

                switch ($_POST['form']) {
                    case "a":
                        if(empty(trim($_POST["name"]))){
                            $name_err = "Please enter a name.";
                        } else{
                    
                            $sql = "SELECT id FROM categories WHERE name = ?";
                            
                            if($stmt = mysqli_prepare($link, $sql)){
                    
                                mysqli_stmt_bind_param($stmt, "s", $param_name);
                                
                    
                                $param_name = trim($_POST["name"]);
                    
                                if(mysqli_stmt_execute($stmt)){
                                    mysqli_stmt_store_result($stmt);
                                    
                                    if(mysqli_stmt_num_rows($stmt) == 1){
                                        $name_err = "This name is already taken.";
                                    } else{
                                        $name = trim($_POST["name"]);
                                    }
                                } else{
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                            }
                    
                            mysqli_stmt_close($stmt);
                        }
            
                        $content = trim($_POST["content"]);
                        $parent_id = trim($_POST["parent_id"]);
            
                        if(empty($name_err) && empty($parent_id_err)){
                        
                            $sql = "INSERT INTO categories (parent_id, name, content) VALUES (?, ?, ?)";
                            
                            if($stmt = mysqli_prepare($link, $sql)){
                                mysqli_stmt_bind_param($stmt, "sss", $param_parent_id, $param_name, $param_content);
                                
                                $param_name = $name;
                                $param_parent_id = $parent_id;
                                $param_content = $content;
                                
                                if(mysqli_stmt_execute($stmt)){
                                    header("location: welcome.php");
                                } else{
                                    echo "Something went wrong. Please try again later.";
                                }
                            }
                            
                            mysqli_stmt_close($stmt);
                        }
                        break;
            
                    case "b":
                    
                        $parent_id_delete = trim($_POST["parent_id_delete"]);
    
                        
            
                        if(empty($parent_id_delete_err)){
                        
                            $sql = "DELETE FROM categories WHERE id = ?";
                            
                            if($stmt = mysqli_prepare($link, $sql)){
                                mysqli_stmt_bind_param($stmt, "s", $parent_id_delete);
                           
                                $param_parent_id_delete = $parent_id_delete;

                                if(mysqli_stmt_execute($stmt)){
                                    header("location: welcome.php");
                                } else{
                                    echo "Something went wrong. Please try again later.";
                                }
                            }
                            
                            mysqli_stmt_close($stmt);
                        }

                        break;

                    case "c":

                        if (empty(trim($_POST["id_update"]))) {
                            $id_update_err = "Select category!";
                        } else{
                            $id_update = $_POST["id_update"];
                        }
                            
                        if (empty(trim($_POST["upd_name"]))) {
                            $upd_name_err = "Please enter a name.";
                        } else {
                            $sql = "SELECT id FROM categories WHERE name = ?";
                        
                                if($stmt = mysqli_prepare($link, $sql)){
                    
                                mysqli_stmt_bind_param($stmt, "s", $param_upd_name);
                                
                    
                                $param_upd_name = trim($_POST["upd_name"]);
                    
                                if(mysqli_stmt_execute($stmt)){
                                    mysqli_stmt_store_result($stmt);
                                    
                                    if(mysqli_stmt_num_rows($stmt) == 1){
                                        $upd_name_err = "This name is already taken.";
                                    } else{
                                        $upd_name = trim($_POST["upd_name"]);
                                    }
                                } else{
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                            }
                
                            mysqli_stmt_close($stmt);
                        }

                        $upd_content = trim($_POST["upd_content"]);
                        $parent_id_update = trim($_POST["parent_id_update"]);

                        if(empty($id_update_err) && empty($upd_name_err)){
                            
                            $sql = "UPDATE categories SET parent_id = ?, name = ?, content = ? WHERE categories.id = ?";
                            
                            if($stmt = mysqli_prepare($link, $sql)){
                                mysqli_stmt_bind_param($stmt, "ssss", $param_parent_id_update, $param_upd_name, $param_upd_content, $param_id_update);
                           
                                $param_parent_id_update = $parent_id_update;
                                $param_upd_name = $upd_name;
                                $param_upd_content = $upd_content; 
                                $param_id_update = $id_update;

                                if(mysqli_stmt_execute($stmt)){
                                    header("location: welcome.php");
                                } else{
                                    echo "Something went wrong. Please try again later.";
                                }
                            }
                            mysqli_stmt_close($stmt);
                        }

                        break;
                    } 
                    
                
            } 
        }
        ?>

        <div class="container form">
            <h2 class="header">Create new category</h2>
            <p>Please fill this form to create new category.</p>
            <form name="a" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="form" value="a">
                <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                    <input type="text" name="name" class="form-control" placeholder="name" value="<?php echo $name; ?>">
                    <span class="help-block"><?php echo $name_err; ?></span>
                </div>    
                <div class="form-group">
                    <input type="text" name="content" class="form-control" placeholder="content" value="<?php echo $content; ?>">
                </div>
                <select id="selectParent" name="category" onchange="parentSelect()">
                <option value="0">Select Parent</option>
                    <?php categoryTree();?>
                </select>

                <div class="form-group hidden <?php echo (!empty($parent_id_err)) ? 'has-error' : ''; ?>">
                    <input id="parent_id" type="text" name="parent_id" class="form-control" placeholder="Select parent" value="<?php echo $parent_id; ?>">
                    <span class="help-block"><?php echo $parent_id_err; ?></span>
                </div>
                <div class="form-group submit">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </form>
        </div>  

        <div class="container form">
            <h2 class="header">Delete Data</h2>
            <p>Please fill this form to delete category.</p>
            <form name="b" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="form" value="b">
                <select id="selectParentDel" name="category" onchange="parentSelectDel()">
                <option value="0">Select category</option>
                    <?php categoryTree();?>
                </select>
                <div class="form-group hidden <?php echo (!empty($parent_id_delete_err)) ? 'has-error' : ''; ?>">
                    <input id="parent_id_delete" type="text" name="parent_id_delete" class="form-control" placeholder="Select parent" value="<?php echo $parent_id_delete; ?>">
                    <span class="help-block"><?php echo $parent_id_delete_err; ?></span>
                </div>
                <div class="form-group submit">
                    <input type="submit" class="btn btn-primary" value="Delete">
                </div>
            </form>
        </div>  

        <div class="container form">
            <h2 class="header">Update Data</h2>
            <p>Please fill this form to update category.</p>
            <form name="c" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="form" value="c">

                <select id="idSelect" name="category" onchange="IdSelect()">
                <option value="0">Select category</option>
                    <?php categoryTree();?>
                </select>
                <div class="form-group hidden <?php echo (!empty($id_update_err)) ? 'has-error' : ''; ?>">
                    <input id="id_Select" type="text" name="id_update" class="form-control" placeholder="Select parent" value="<?php echo $id_update; ?>">
                    <span class="help-block"><?php echo $id_update_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($upd_name_err)) ? 'has-error' : ''; ?>">
                    <input type="text" name="upd_name" class="form-control" placeholder="New name" value="<?php echo $upd_name; ?>">
                    <span class="help-block"><?php echo $upd_name_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($upd_content_err)) ? 'has-error' : ''; ?>">
                    <input id="" type="text" name="upd_content" class="form-control" placeholder="New content" value="<?php echo $upd_content; ?>">
                    <span class="help-block"><?php echo $upd_content_err; ?></span>
                </div>

                <select id="selectParentIdUpd" name="category" onchange="parentSelectUpd()">
                <option value="0">Select new parent</option>
                    <?php categoryTree(); mysqli_close($link);?>
                </select>
                <div class="form-group hidden <?php echo (!empty($parent_id_update_err)) ? 'has-error' : ''; ?>">
                    <input id="parent_id_update" type="text" name="parent_id_update" class="form-control" placeholder="Select parent" value="<?php echo $parent_id_update; ?>">
                    <span class="help-block"><?php echo $parent_id_update_err; ?></span>
                </div>



                <div class="form-group submit">
                    <input type="submit" class="btn btn-primary" value="Update">
                </div>
            </form>
        </div>  



        <p class="fleft">
            <a href="logout.php" class="btn btn-danger">Sign Out</a>
        </p>
    </div>
<script src="content.js"></script>

</body>
</html>