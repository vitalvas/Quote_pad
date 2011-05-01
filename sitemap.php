<?php
define('PROTECT', true);
require_once('config.php');
$dbh = mysql_connect($db_host,$db_user,$db_pass) or  die('No connected : '.mysql_error());
mysql_select_db($db_name,$dbh) or die("Can't select db : ".mysql_error());
mysql_query("SET NAMES cp1251");

$dom = new DomDocument('1.0', 'UTF-8');
$dom -> formatOutput = true;

$loc=$dom->createElement('urlset');
$urlset = $dom->appendChild($loc);

$xmlns = $dom->createAttribute("xmlns");
$loc->appendChild($xmlns);
$val = $dom->createTextNode("http://www.sitemaps.org/schemas/sitemap/0.9");
$xmlns->appendChild($val);

$xmlns = $dom->createAttribute("xmlns:xsi");
$loc->appendChild($xmlns);
$val = $dom->createTextNode("http://www.w3.org/2001/XMLSchema-instance");
$xmlns->appendChild($val);

$xmlns = $dom->createAttribute("xsi:schemaLocation");
$loc->appendChild($xmlns);
$val = $dom->createTextNode("http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");
$xmlns->appendChild($val);

$result = mysql_query("select id,postdate from posts where status='enabled'");
while ($res = mysql_fetch_assoc($result)) {
    $id = $res['id'];
    $date = explode(' ', $res['postdate']);
    $date = $date['0'];
    $url = $urlset -> appendChild($dom->createElement('url'));
    $loc = $url -> appendChild($dom->createElement('loc'));
    $text = $loc -> appendChild($dom->createTextNode("http://bash.vitalvas.uz.ua/quote/$id"));
    $lastmod = $url -> appendChild($dom->createElement('lastmod'));
    $text = $lastmod -> appendChild($dom->createTextNode("$date"));
    $changefreq = $url -> appendChild($dom->createElement('changefreq'));
    $text = $changefreq -> appendChild($dom->createTextNode('daily'));
    $priority = $url -> appendChild($dom->createElement('priority'));
    $text = $priority -> appendChild($dom->createTextNode('0.8'));
}

$dom->formatOutput = true;
$out = $dom->saveXML();
$dom->save('sitemap.xml');
echo $out;

?>