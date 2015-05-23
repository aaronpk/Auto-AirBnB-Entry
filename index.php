<?php
require 'vendor/autoload.php';
require 'inc.php';

$config = yaml_parse(file_get_contents('config.yml'));
?>
<html>
<head>
  <style type="text/css">
    body {
      padding: 20px;
      font-family: sans-serif;
    }
    .input-group {
      margin-bottom: 10px;
      font-size: 14pt;
    }
    .input-group select, .input-group input {
      font-size: 14pt;
      padding: 3px 10px;
    }
    .btn {
      border: 1px #ccc solid;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  
  <form action="/post.php" method="post">
  
  <div class="input-group">
    <select name="location">
    <?php foreach($config['locations'] as $location): ?>
      <option value="<?= $location['name'] ?>"><?= $location['name'] ?></option>
    <?php endforeach; ?>
    </select>
  </div>
  
  <div class="input-group">
    Password: <input type="password" name="password">
  </div>
  
  <div class="input-group">
    Code: <input type="text" name="code">
  </div>
  
  <div class="input-group">
    <input type="submit" value="Set Code" class="btn">
  </div>  
  
  </form>
  
</body>
</html>