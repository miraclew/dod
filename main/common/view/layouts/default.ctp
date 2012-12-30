<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php strtolower(config('encoding')) ?>" />
    <title>
        <?php echo 'test' ?>:
    </title>
    <link href="<?php echo $this->webroot(); ?>favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?php echo $this->webroot(); ?>favicon.ico" type="image/x-icon" rel="shortcut icon">
<?php if (config('debug')): ?>
    <link rel="stylesheet" type="text/css" href="<?php $this->webroot() ?>css/internal.css">    
<?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?php $this->webroot() ?>css/common.css">
<?php
    echo $scripts_for_layout;
?>
<style type="text/css">
<!--
* {
    margin:0;
    padding:0;
}
body {
    font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
    background: #003d4c;/*#42413C;*/
    color: #000;
}

h1, h2, h3, h4, h5, h6, p {  
    padding-right: 15px;
    padding-left: 15px; 
    font-weight: normal;
    margin-bottom: 0.5em;
}
a img { 
    border: none;
}

a:link {
    color: #42413C;
    text-decoration: underline;
}
a:visited {
    color: #6E6C64;
    text-decoration: underline;
}
a:hover, a:active, a:focus { 
    text-decoration: none;
}

.container {
    width: 960px;
    background: #FFF;
    margin: 0 auto;
}

.header {
    background: #ADB96E;
    height:70px;
}

.sidebar1 {
    float: left;
    width: 180px;
    background: #EADCAE;
    padding-bottom: 10px;
}
.content {
    padding: 10px 0;
    width: 780px;
    float: left;
}

.content ul, .content ol { 
    padding: 0 15px 15px 40px; 
}

ul.nav {
    list-style: none; 
    border-top: 1px solid #666;
    margin-bottom: 1px; 
}
ul.nav li {
    border-bottom: 1px solid #666;
}
ul.nav a, ul.nav a:visited { 
    padding: 5px 5px 5px 15px;
    display: block; 
    width: 160px;  
    text-decoration: none;
    background: #C6D580;
}
ul.nav a:hover, ul.nav a:active, ul.nav a:focus { 
    background: #ADB96E;
    color: #FFF;
}


.footer {
    height: 15px;
    padding: 10px 0;
    background: #CCC49F;
    position: relative;
    clear: both;
}

.fltrt {  
    float: right;
    margin-left: 8px;
}
.fltlft { 
    float: left;
    margin-right: 8px;
}
.clearfloat { 
    clear:both;
    height:0;
    font-size: 1px;
    line-height: 0px;

div.form,
div.index,
div.view {
    float:right;
    width:76%;
    border-left:1px solid #666;
    padding:10px 2%;
}
div.actions {
    float:left;
    width:16%;
    padding:10px 1.5%;
}
div.actions h3 {
    padding-top:0;
    color:#777;
}    

/** Tables **/
table {
    border-right:0;
    clear: both;
    color: #333;
    margin-bottom: 10px;
    width: 100%;
}
th {
    border:0;
    border-bottom:2px solid #555;
    text-align: left;
    padding:4px;
}
th a {
    display: block;
    padding: 2px 4px;
    text-decoration: none;
}
th a.asc:after {
    content: ' ?';
}
th a.desc:after {
    content: ' ?';
}
table tr td {
    padding: 6px;
    text-align: left;
    vertical-align: top;
    border-bottom:1px solid #ddd;
}
table tr:nth-child(even) {
    background: #f9f9f9;
}
td.actions {
    text-align: center;
    white-space: nowrap;
}
table td.actions a {
    margin: 0px 6px;
    padding:2px 5px;
}
-->
</style>
</head>
<body>
<?php
    echo $content_for_layout;
?>
</body>
</html>