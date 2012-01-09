<?PHP

require('rsdns.php');

$comm = new rsdns('USERNAME','APIKEY','0');


print $comm->expires . '<br />';
print $comm->account . '<br />';
print '+++++++++++++++++++limit_all++++++++++++++++++<br />';
//$comm->traverse(json_decode($comm->limit_all()));
print '+++++++++++++++++++limit_types++++++++++++++++<br />';
//$comm->traverse(json_decode($comm->limit_types()));
print '+++++++++++++++++++limit_check++++++++++++++++<br />';
//$comm->traverse(json_decode($comm->limit_check('DOMAIN_LIMIT')));
print '+++++++++++++++++++domain_list++++++++++++++++<br />';
//$comm->traverse(json_decode($comm->domain_list()));
print '+++++++++++++++++++domain_search++++++++++++++<br />';
//well the search does not work right :/
print "search goes slow and does not filter right<br />";
#$comm->traverse(json_decode($comm->domain_search('throwin-granades.org')));
print '+++++++++++++++++++domain_details++++++++++++++<br />';
print "domain details dissabled to speed up list<br />";
#$comm->traverse(json_decode($comm->domain_deatils('1621023',false,true)));
print '+++++++++++++++++++domain_changes++++++++++++++<br />';
//$date = new DateTime(strtotime(time()));
//$date = $date->format('Y-m-dTH:i:s.000+0000');
print "Test string not used: ". $date . "<br />";
#$comm->traverse(json_decode($comm->domain_changes('1621023')));
print '+++++++++++++++++++domain_export++++++++++++++<br />';
print "Trying to showcase status as a reporting function. <br /> This looks like its working and then didn't?<br />";
#$comm->traverse(json_decode($comm->domain_export('1621023',0)));
print '+++++++++++++++++++domain_create++++++++++++++<br />';
#$comm->traverse(json_decode($comm->domain_create($json_data,0)));
print '+++++++++++++++++++domain_import++++++++++++++<br />';
#$comm->traverse(json_decode($comm->domain_import($Bind_data,0)));
print '+++++++++++++++++++domain_modify++++++++++++++<br />';
#$comm->traverse(json_decode($comm->domain_modiy($json_data,0)));
print '+++++++++++++++++++domain_modify_any++++++++++++++<br />';
#$comm->traverse(json_decode($comm->domain_modify_any($json_data,0)));
print '+++++++++++++++++++domain_remove++++++++++++++<br />';
#$comm->traverse(json_decode($comm->domain_remove($Array_Domain_IDs,0)));
print '+++++++++++++++++++subdomain_list+++++++++++++<br />';
//does a string comparison of some kind so it's slow : /
print "subdomain_list works but is slow<br />";
#$comm->traverse(json_decode($comm->subdomain_list('1621023')));

print '+++++++++++++++++++record_list++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_list('1621023')));
print '+++++++++++++++++++record_list_id++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_list_id('1621023',$recordID)));
print '+++++++++++++++++++record_add++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_add('1621023',$config,0)));
print '+++++++++++++++++++record_modify++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_modify('1621023',$recordID,$config,0)));
print '+++++++++++++++++++record_modify_any++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_modify_any('1621023',$config,0)));
print '+++++++++++++++++++record_remove++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_remove('1621023',$recordID,$config,0)));
print '+++++++++++++++++++record_remove_any++++++++++++++++<br />';
#$comm->traverse(json_decode($comm->record_remove_any('1621023',$config,0)));
print '+++++++++++++++++++++++++++++++++++++++++++++++<br />';
print "Finished";



?>