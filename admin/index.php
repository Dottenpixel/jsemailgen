<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link type="text/css" href="/css/custom-theme/jquery-ui-1.7.1.custom.css" rel="stylesheet" />	
<link type="text/css" href="/css/admin.css" rel="stylesheet" />	
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript">
    $(function() {
        $(".datePicker").datepicker();
    });
</script>
</head>

<body>
    <div id="adminCont">
        <div id="contentCont">
        	<h1>Admin</h1>
            <ul id="adminNav">
                <li><a href="addNewsletter.php">Newsletters</a></li>
                <li><a href="addNewsArticle.php">News Articles</a></li>
                <li><a href="viewContactFormSubmissions.php">Contact Form Submissions</a></li>
            </ul>
    	</div>
    </div>
</body>
</html>