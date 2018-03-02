<?php

add_hook('AdminAreaClientSummaryActionLinks', 1, function($vars) {
    $return = [];

    $where = "clientid=" . (int)$vars['userid'];
    $numrows = get_query_val("mod_passwordmanager", "COUNT(id)", $where);
    //if ($vars['userid'] == 1) {
        $return[] = '<a href="addonmodules.php?module=passwordmanager&userid='.$vars['userid'].'"><img src="images/icons/resetpw.png" border="0" align="absmiddle"> Password Manager Entries ('.$numrows.')</a>';
    //}

    return $return;
});



add_hook('AdminAreaHeadOutput', 1, function($vars) {
    if ($vars['filename']=='addonmodules' && $vars['pagetitle']=='Password Manager Addon') {
    return <<<HTML
<script type="text/javascript">
                    $(document).ready(function(){
                $( "a.tab-top" ).click( function() {
    var tabId = $(this).data('tab-id');
    $("#tab").val(tabId);
    window.location.hash = 'tab=' +  + tabId;
});

var selectedTab = 0;

if (selectedTab == 0) {
    refreshedTab = window.location.hash;
    if (refreshedTab) {
        refreshedTab = refreshedTab.substring(5);
        $("a[href='#tab" +  + refreshedTab + "']").click();
    }
}

/**
 * We want to make the adminTabs on this page toggle
 */
$( "a[href^='#tab']" ).click( function() {
    var tabID = $(this).attr('href').substr(4);
    var tabToHide = $("#tab" + tabID);
    if(tabToHide.hasClass('active')) {
        tabToHide.removeClass('active');
    }  else {
        tabToHide.addClass('active')
    }
});
            });
</script>
HTML;
}
});


?>