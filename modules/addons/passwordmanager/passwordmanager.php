<?php

#############################################
#
# WHMCS Password Manager Addon Module
# @ Version  : 1.0
# @ Author   : UnifiedWebs (don robertson)
# @ Release  : 2017-10-10
# @ Website  : http://www.unifiedwebs.com
#
#############################################


if (!defined("WHMCS")) {
  exit("This file cannot be accessed directly");
}

function listadmins(){
  $options=array();
  $options[0]='none';
  $resulta = select_query("tbladmins", "id,username", "", "username", "ASC");
    while ($dataa = mysql_fetch_array($resulta)) {
      $id = $dataa['id'];
      $adminusername = $dataa['username'];
        $options[$id]=$adminusername;
    }
return $options;
}

function passwordmanager_config() {
    $adminOptions = listadmins();
    $configarray = array(
    "name" => "Password Manager Addon",
    "description" => "This is a Password Manager addon module. This addon can be used to securely store company login details to various sources.",
    "version" => "1.0",
    "author" => "UnifiedWebs.com",
    "fields" => array(
                // the dropdown field type renders a select menu of options
                'GlobalAdmin' => array(
                    'FriendlyName' => 'Global Admin',
                    'Type' => 'dropdown',
                    'Options' => $adminOptions,
                    'Description' => 'Choose an admin with global read/write permissions',
                ),
            )
    );
    return $configarray;
}

function passwordmanager_activate() {
    # Create Custom DB Table
  $query = "CREATE TABLE `mod_passwordmanager` (
        `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `loginurl` VARCHAR(255) NOT NULL,
        `username` VARCHAR(255) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `notes` TEXT NOT NULL,
        `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `admin_r_perms` VARCHAR(255) NOT NULL,
        `admin_rw_perms` VARCHAR(255) NOT NULL,
        `last_mod_adminid` INT(10) NOT NULL,
        `adminid` INT(10) NOT NULL,
        `clientid` INT(10) NOT NULL
       ) ; ";
  $result = full_query($query);

    # Return Result
    if($result){
      return array('status'=>'success','description'=>'The Password Manager addon module has been successfully activated!');
    }else{
      return array('status'=>'error','description'=>'There was an error activating the Password Manager addon module. Please try again later.');
    }
    //return array('status'=>'info','description'=>'');
}

function passwordmanager_deactivate() {
  // check for existing entries
  $result = select_query("mod_passwordmanager", "id", "", "id", "ASC");
    $id_exist='';
    while ($data = mysql_fetch_array($result)) {
      $id_exist = $data['id'];
    }

    if($id_exist){
        return array('status'=>'error','description'=>'There was an error removing the Password Manager addon module. You can not remove it while you have existing stored entries. Please delete all entires then try again.');
    }else{
        # Remove Custom DB Table
        $query = "DROP TABLE `mod_passwordmanager`";
        $result = full_query($query);
        # Return Result
        return array('status'=>'success','description'=>'Removal of the Password Addon module was successful.');
    }
    //return array('status'=>'info','description'=>'');
}




function var_encode($string) {
$crpassword="b7Sgdo2R0Mz";
$encrypted_string=openssl_encrypt($string,"AES-128-ECB",$crpassword);
return $encrypted_string;
}
function var_decode($string) {
$crpassword="b7Sgdo2R0Mz";
$decrypted_string=openssl_decrypt($string,"AES-128-ECB",$crpassword);
return $decrypted_string;
}


function passwordmanager_output($vars) {
  date_default_timezone_set('America/New_York');

  global $whmcs;
  global $CONFIG;
  global $aInt;
  global $numrows;
  global $page;
  global $limit;
  global $order;
  global $orderby;
  global $jquerycode;
  global $jscode;
  global $attachments_dir;

    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $LANG = $vars['_lang'];
    $action = $_REQUEST['action'];
    $GlobalAdmin = (int)$vars['GlobalAdmin'];

if($_GET['userid']){
  $userid=(int)$_GET['userid'];
}


if($action=='add'){

  if($_POST['save']=='1'){
    $table = "mod_passwordmanager";
    $multi_admin_r_access_ids = implode(', ', $_POST['multi_admin_r_access_id']);
    $multi_admin_rw_access_ids = implode(', ', $_POST['multi_admin_rw_access_id']);
    $values = array("name"=>$_POST['pass_name'],"loginurl"=>$_POST['pass_loginurl'],"username"=>var_encode($_POST['pass_username']),"password"=>var_encode($_POST['pass_password']),"notes"=>var_encode($_POST['pass_notes']), "date_added"=>'now()', "date_modified"=>'now()', "admin_r_perms"=>$multi_admin_r_access_ids, "admin_rw_perms"=>$multi_admin_rw_access_ids, "adminid"=>$_SESSION['adminid'], "last_mod_adminid"=>$_SESSION['adminid'], "clientid"=>$_POST['pass_clientid']);
    $newid = insert_query($table,$values);
      if($newid){
        redir("module=passwordmanager");
        exit();
      }
  }

    if($userid){
      $modulelink_add = $modulelink.'&userid='.$userid;
    }else{
      $modulelink_add = $modulelink;
    }

echo '<div class="admin-tabs"><div class="context-btn-container"><button type="button" class="btn btn-default" onclick="window.location=\''.$modulelink_add.'\'"><i class="fa fa-chevron-left"></i> View Passwords </button></div></div>';
echo '<h2>Add New Entry</h2>';
echo "<form method=\"post\" action=\"" . $modulelink . "&action=add\">
<input type=\"hidden\" name=\"save\" value=\"1\" />";
echo "<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Name</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_name\" size=\"70\" value=\"";
echo "\" required /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Login URL</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_loginurl\" size=\"70\" value=\"";
echo "\" required /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Username</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_username\" size=\"70\" value=\"";
echo "\" required /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Password</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_password\" size=\"70\" value=\"";
echo "\" required /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Notes</td><td class=\"fieldarea\">
<textarea name=\"pass_notes\" id=\"note\" class=\"pr-body\" cols=\"50\" rows=\"6\"></textarea>";
echo "</td></tr>";
// spacer row
echo "<tr><td width=\"200\" class=\"fieldlabel\">&nbsp;</td><td class=\"fieldarea\">&nbsp;</td></tr>";

echo "<tr><td width=\"200\" class=\"fieldlabel\">Linked Client</td><td class=\"fieldarea\">";
$clientdropdown = "<select name=\"pass_clientid\" style=\"font-size:16px;\"><option value=\"\">None</option>";
$resultz = select_query("tblclients", "id,firstname,lastname,companyname", "", "firstname` ASC,`lastname", "ASC");
while ($dataz = mysql_fetch_array($resultz)) {
  $cid = $dataz['id'];
  $clientfirstname = $dataz['firstname'];
  $clientlastname = $dataz['lastname'];
  $clientcompanyname = $dataz['companyname'];
  $clientdropdown .= "<option value=\"" . $cid . "\"";
  if($userid==$cid){
    $clientdropdown .= " selected";
  }
    if($clientcompanyname){
    $clientdropdown .= ">" . $clientfirstname . " " . $clientlastname . " (".$clientcompanyname.")</option>";
  }else{
    $clientdropdown .= ">" . $clientfirstname . " " . $clientlastname . "</option>";
  }
}
echo $clientdropdown;
echo "</td></tr>";


   echo '<tr>
        <td width="200" class="fieldlabel">Admin Access</td>
        <td class="fieldarea">
       <div style="float: left;width: 515px;">
            <select name="multi_admin_r_access_id[]" class="form-control selectize-multi-select input-inline" multiple data-value-field="id" placeholder="Select User">';

$resulta = select_query("tbladmins", "id,username", "", "username", "ASC");
$admindropdown='';
$is_sel=0;
while ($dataa = mysql_fetch_array($resulta)) {
  $aid = $dataa['id'];
  $adminusername = $dataa['username'];
  $admindropdown .= "<option value=\"" . $aid . "\"";
  if($userid==$aid){
    $admindropdown .= " selected";
    $is_sel='1';
  }
  $admindropdown .= ">" . $adminusername . "</option>";
}
echo '<option value="999999"';
if(!$is_sel){ echo " selected"; }
echo '>All Admins</option>';
echo $admindropdown;

              echo '</select>
        </div><div style="padding-top:5px;"> &nbsp; Read Access</div>
<br>
        <div style="float: left;width: 515px;">
            <select name="multi_admin_rw_access_id[]" class="form-control selectize-multi-select input-inline" multiple data-value-field="id" placeholder="Select User">';

$resulta = select_query("tbladmins", "id,username", "", "username", "ASC");
$admindropdown='';
$is_sel=0;
while ($dataa = mysql_fetch_array($resulta)) {
  $aid = $dataa['id'];
  $adminusername = $dataa['username'];
  $admindropdown .= "<option value=\"" . $aid . "\"";
  if($userid==$aid){
    $admindropdown .= " selected";
    $is_sel='1';
  }
  $admindropdown .= ">" . $adminusername . "</option>";
}
echo '<option value="999999"';
if(!$is_sel){ echo " selected"; }
echo '>All Admins</option>';
echo $admindropdown;

                echo '</select>
        </div><div style="padding-top:5px;"> &nbsp; Read/Write Access</div>
        </td>
    </tr>';


echo "</table><br><p align=\"center\"><input class=\"btn btn-primary\" type=\"submit\" value=\"Add Entry\" /></p></form><br>";

}







if($action=='edit'){
      $id=$_GET['id'];

  if($_POST['save']=='1'){
    $sid=$_GET['sid'];
    $table = "mod_passwordmanager";
    $multi_admin_r_access_ids = implode(', ', $_POST['multi_admin_r_access_id']);
    $multi_admin_rw_access_ids = implode(', ', $_POST['multi_admin_rw_access_id']);
    $update = array("date_modified"=>'now()', "name"=>$_POST['pass_name'],"loginurl"=>$_POST['pass_loginurl'],"username"=>var_encode($_POST['pass_username']),"password"=>var_encode($_POST['pass_password']),"notes"=>var_encode($_POST['pass_notes']),"admin_r_perms"=>$multi_admin_r_access_ids, "admin_rw_perms"=>$multi_admin_rw_access_ids,"last_mod_adminid"=>$_SESSION['adminid'],"clientid"=>$_POST['pass_clientid']);
    $where = array("id"=>$sid);
      $upid = update_query($table,$update,$where);
        if($upid){
          redir("module=passwordmanager");
          exit();
        }
  }


$table = "mod_passwordmanager";
$fields = "name,loginurl,username,password,notes,date_added,date_modified,adminid,clientid,last_mod_adminid,admin_r_perms,admin_rw_perms";
$where = array("id"=>$id);
$result = select_query($table,$fields,$where);
$data = mysql_fetch_array($result);
$name = $data['name'];
$loginurl = $data['loginurl'];
$username = var_decode($data['username']);
$password = var_decode($data['password']);
$notes = var_decode($data['notes']);
$date_added = fromMySQLDate( $data['date_added'], true );
$date_modified = fromMySQLDate( $data['date_modified'], true );
$admin_r_perms = $data['admin_r_perms'];
if($admin_r_perms){
$admin_r_perms=explode(",",$admin_r_perms);
$admin_r_perms=array_filter($admin_r_perms);
}
$admin_rw_perms = $data['admin_rw_perms'];
if($admin_rw_perms){
$admin_rw_perms=explode(",",$admin_rw_perms);
$admin_rw_perms=array_filter($admin_rw_perms);
}
$last_mod_adminid = $data['last_mod_adminid'];
$adminid = $data['adminid'];
$clientid = $data['clientid'];

$read_access=false;
$write_access=false;
// no access if empty
if(!$admin_rw_perms){
  $write_access=false;
}
if(!$admin_r_perms){
  $read_access=false;
}
// access if adminid in perms id list
if(in_array($_SESSION['adminid'], $admin_rw_perms)){
  $read_access=true;
  $write_access=true;
}
if(in_array($_SESSION['adminid'], $admin_r_perms)){
  $read_access=true;
}
// all admins has access
if(in_array('999999', $admin_r_perms)){
  $read_access=true;
}
if(in_array('999999', $admin_rw_perms)){
  $read_access=true;
  $write_access=true;
}
// owner has full access
if($_SESSION['adminid']==$adminid){
  $read_access=true;
  $write_access=true;
}
// global admin has super full access
if($_SESSION['adminid']==$GlobalAdmin){
  $read_access=true;
  $write_access=true;
}


  echo '<div class="admin-tabs"><div class="context-btn-container"><button type="button" class="btn btn-default" onclick="window.location=\''.$modulelink.'\'"><i class="fa fa-chevron-left"></i> View Passwords </button> &nbsp; <button type="button" class="btn btn-success" onclick="window.location=\''.$modulelink.'&action=add\'"><i class="fa fa-plus"></i> Add New </button></div></div>';
echo '<h2>Edit Entry</h2>';

// check perms
if($read_access==true){

echo "<form method=\"post\" action=\"" . $modulelink . "&action=edit&sid=".$id."\">
<input type=\"hidden\" name=\"save\" value=\"1\" />";
echo "<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Name</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_name\" size=\"70\" value=\"";
echo $name;
echo "\" required ";
if(!$write_access){echo ' disabled ';}
echo "/></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Login URL</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_loginurl\" size=\"70\" value=\"";
echo $loginurl;
echo "\" required ";
if(!$write_access){echo ' disabled ';}
echo "/>";
$vurl = strpos($loginurl, 'http') !== 0 ? "http://$loginurl" : $loginurl;
if (filter_var($vurl, FILTER_VALIDATE_URL)) { 
echo '&nbsp; <a href="'.$vurl.'" target="_blank">Visit Link</a>';
}
echo "</td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Username</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_username\" size=\"70\" value=\"";
echo $username;
echo "\" required ";
if(!$write_access){echo ' disabled ';}
echo "/></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Password</td><td class=\"fieldarea\"><input type=\"text\" name=\"pass_password\" size=\"70\" value=\"";
echo $password;
echo "\" required ";
if(!$write_access){echo ' disabled ';}
echo "/></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Notes</td><td class=\"fieldarea\">
<textarea name=\"pass_notes\" id=\"note\" class=\"pr-body\" cols=\"50\" rows=\"6\"";
if(!$write_access){echo ' disabled ';}
echo ">";
echo $notes;
echo "</textarea></td></tr>";
// spacer row
echo "<tr><td width=\"200\" class=\"fieldlabel\">&nbsp;</td><td class=\"fieldarea\">&nbsp;</td></tr>";

echo "<tr><td width=\"200\" class=\"fieldlabel\">Linked Client</td><td class=\"fieldarea\">";
$clientdropdown = "<select name=\"pass_clientid\" style=\"font-size:16px;\"";
if(!$write_access){$clientdropdown .= ' disabled';}
$clientdropdown .= "><option value=\"\">None</option>";
$resultz = select_query("tblclients", "id,firstname,lastname,companyname", "", "firstname` ASC,`lastname", "ASC");
//$c_sel=0;
while ($dataz = mysql_fetch_array($resultz)) {
  $cid = $dataz['id'];
  $clientfirstname = $dataz['firstname'];
  $clientlastname = $dataz['lastname'];
  $clientcompanyname = $dataz['companyname'];
  $clientdropdown .= "<option value=\"" . $cid . "\"";
if($clientid==$cid){
$clientdropdown .= " selected";
$c_sel='1';
}
  if($clientcompanyname){
    $clientdropdown .= ">" . $clientfirstname . " " . $clientlastname . " (".$clientcompanyname.")</option>";
  }else{
    $clientdropdown .= ">" . $clientfirstname . " " . $clientlastname . "</option>";
  }
}
echo $clientdropdown.'</select>';
if($c_sel) { 
echo '&nbsp; <a href="clientssummary.php?userid='.$clientid.'" target="_blank">Client Summary</a>';
}
echo "</td></tr>";


   echo '<tr>
        <td width="200" class="fieldlabel">Admin Access</td>
        <td class="fieldarea">
       <div style="float: left;width: 515px;">
            <select name="multi_admin_r_access_id[]" class="form-control selectize-multi-select input-inline" multiple data-value-field="id" placeholder="Select User"';
            if(!$write_access){echo' disabled';}
            echo '>';

$resulta = select_query("tbladmins", "id,username", "", "username", "ASC");
$admindropdown='';
$is_sel=0;
while ($dataa = mysql_fetch_array($resulta)) {
  $aid = $dataa['id'];
  $adminusername = $dataa['username'];
  $admindropdown .= "<option value=\"" . $aid . "\"";
  if(in_array($aid, $admin_r_perms)){
    $admindropdown .= " selected";
    $is_sel='1';
  }
  $admindropdown .= ">" . $adminusername . "</option>";
}
echo '<option value="999999"';
if(in_array('999999', $admin_r_perms)){ echo " selected"; }
echo '>All Admins</option>';
echo $admindropdown;

              echo '</select>
        </div><div style="padding-top:5px;"> &nbsp; Read Access</div>
<br>
        <div style="float: left;width: 515px;">
            <select name="multi_admin_rw_access_id[]" class="form-control selectize-multi-select input-inline" multiple data-value-field="id" placeholder="Select User"';
            if(!$write_access){echo' disabled';}
            echo '>';

$resulta = select_query("tbladmins", "id,username", "", "username", "ASC");
$admindropdown='';
$is_sel=0;
while ($dataa = mysql_fetch_array($resulta)) {
  $aid = $dataa['id'];
  $adminusername = $dataa['username'];
  $admindropdown .= "<option value=\"" . $aid . "\"";
  if(in_array($aid, $admin_rw_perms)){
    $admindropdown .= " selected";
    $is_sel='1';
  }
  $admindropdown .= ">" . $adminusername . "</option>";
}
echo '<option value="999999"';
if(in_array('999999', $admin_rw_perms)){ echo " selected"; }
echo '>All Admins</option>';
echo $admindropdown;

                echo '</select>
        </div><div style="padding-top:5px;"> &nbsp; Read/Write Access</div>
        </td>
    </tr>';




$result2 = select_query("tbladmins", "username", array("id" => $adminid));
$data2 = mysql_fetch_array($result2);
$result3 = select_query("tbladmins", "username", array("id" => $last_mod_adminid));
$data3 = mysql_fetch_array($result3);
echo "<tr><td width=\"200\" class=\"fieldlabel\">Owner</td><td class=\"fieldarea\"><input type=\"text\" name=\"adminid\" size=\"18\" value=\"";
echo $data2['username'];
echo "\" disabled /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Date Added</td><td class=\"fieldarea\"><input type=\"text\" name=\"date_added\" size=\"18\" value=\"";
echo $date_added;
echo "\" disabled /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Last Modified</td><td class=\"fieldarea\"><input type=\"text\" name=\"date_modified\" size=\"18\" value=\"";
echo $date_modified;
echo "\" disabled /></td></tr>";
echo "<tr><td width=\"200\" class=\"fieldlabel\">Last Modified By</td><td class=\"fieldarea\"><input type=\"text\" name=\"last_mod_adminid\" size=\"18\" value=\"";
echo $data3['username'];
echo "\" disabled /></td></tr>";
echo "</table><br><p align=\"center\">"; 
echo '<button type="button" class="btn btn-default" onclick="window.location=\''.$modulelink.'\'">Cancel</button> &nbsp; ';
echo "<input class=\"btn btn-primary\" type=\"submit\" value=\"Save Changes\"";
if(!$write_access){echo ' disabled';}
echo " /></p></form><br>";
}else{
$result2 = select_query("tbladmins", "username", array("id" => $adminid));
$data2 = mysql_fetch_array($result2);
  echo '<br><p align="center">You do not have permissions to access this entry owned by user "'.$data2['username'].'".</p>';
}
}



if($action=='delete'){
  $did = $_GET['did'];
  delete_query("mod_passwordmanager", array("id" => $did));
  redir("module=passwordmanager");
  exit();
}



  if(!$action){

    if($userid){
      $modulelink_add = $modulelink.'&userid='.$userid;
    }else{
      $modulelink_add = $modulelink;
    }
echo '<div class="admin-tabs"><div class="context-btn-container"><button type="button" class="btn btn-success" onclick="window.location=\''.$modulelink_add.'&action=add\'"><i class="fa fa-plus"></i> Add New </button></div></div>';
    echo '<h2>Password Manager Overview</h2>';


echo '<ul class="nav nav-tabs admin-tabs" role="tablist">
<li><a class="tab-top" href="#tab1" role="tab" data-toggle="tab" id="tabLink1" data-tab-id="1" aria-expanded="true">Search/Filter</a></li>
</ul>';
echo '<div class="tab-content admin-tabs">
  <div class="tab-pane" id="tab1">
<form method="post" action="'.$modulelink_add.'">
<input type="hidden" name="filter" value="true">
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
<tbody><tr><td width="15%" class="fieldlabel">Name</td><td class="fieldarea"><input type="text" name="sname" value="'.$_POST['sname'].'" size="70"></td>
<td width="15%" class="fieldlabel">Linked Client</td><td class="fieldarea">';
$clientdropdown = "<select name=\"sclientid\" style=\"font-size:16px;\"><option value=\"\">Any</option>";
$resultz = select_query("tblclients", "id,firstname,lastname,companyname", "", "firstname` ASC,`lastname", "ASC");
while ($dataz = mysql_fetch_array($resultz)) {
  $cid = $dataz['id'];
  $clientfirstname = $dataz['firstname'];
  $clientlastname = $dataz['lastname'];
  $clientcompanyname = $dataz['companyname'];
  $clientdropdown .= "<option value=\"" . $cid . "\"";
if(($_POST['sclientid']==$cid)OR($userid==$cid)){
$clientdropdown .= " selected";
}
  if($clientcompanyname){
    $clientdropdown .= ">" . $clientfirstname . " " . $clientlastname . " (".$clientcompanyname.")</option>";
  }else{
    $clientdropdown .= ">" . $clientfirstname . " " . $clientlastname . "</option>";
  }
}
echo $clientdropdown;
echo '</td>
</tr>
</tbody></table>
<div class="btn-container">';
if($_POST['filter']){
echo '<button type="button" class="btn btn-default" onclick="window.location=\''.$modulelink.'\'">Reset</button> &nbsp;'; 
}
echo '<input type="submit" value="Search/Filter" class="btn btn-default">
</div>
</form>
  </div></div>';
echo '<br>';


$aInt->sortableTableInit("id", "ASC");
$tabledata = "";
$where = array();

if($_POST['sclientid']!=''){
$userid=$_POST['sclientid'];
}

if($userid){
  $where = array("clientid"=>$userid);
}

if($_POST['sname']!=''){
$where2 = array("name"=>$_POST['sname']);
$where = $where + $where2;
}


$table = "mod_passwordmanager";
$fields = "id,name,loginurl,username,password,notes,date_added,date_modified,clientid,adminid";

  $result = select_query($table, $fields, $where, "id", "DESC", $page * $limit . "," . $limit );
  while ($data = mysql_fetch_array( $result )) {
      $id = $data['id'];
      $name = $data['name'];
      $loginurl = $data['loginurl'];
      $username = $data['username'];
      $password = $data['password'];
      $notes = $data['notes'];
      $date_added = fromMySQLDate( $data['date_added'], true );
      $date_modified = fromMySQLDate( $data['date_modified'], true );
      $clientid = $data['clientid'];
      $adminid = $data['adminid'];

    $name_link = '<a href="'.$modulelink.'&action=edit&id='.$id.'">'.$name.'</a>';
    $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    $loginurl = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $loginurl);
    $edit_link = '<a href="'.$modulelink.'&action=edit&id='.$id.'"><img src="images/edit.gif" width="16" height="16" border="0" alt="Edit"></a>';
    $delete_link = '<a href="'.$modulelink.'&action=delete&did='.$id.'" onclick="return confirm(\'Are you sure you want to delete this entry?\')"><img src="images/delete.gif" width="16" height="16" border="0" alt="Delete"></a>';

    $tabledata[] = array($name_link, $loginurl, $date_modified, $edit_link, $delete_link);
  }
  echo $aInt->sortableTable( array( "Name", "Login URL", "Last Updated", "", "" ), $tabledata );
}




}

?>