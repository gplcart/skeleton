/**
* @package <?php echo $module['name']; ?> 
* @author <?php echo $module['author']; ?> 
* @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
* @license <?php echo $module['license_url']; ?> 
*/
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo '<?php echo $_token; ?>'; ?>">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-group">
        <label class="col-md-2 control-label"><?php echo '<?php echo $this->text("Name"); ?>'; ?></label>
        <div class="col-md-6">
          <input name="settings[name]" class="form-control" placeholder="Placeholder" value="<?php echo '<?php echo isset($settings["name"]) ? $this->escape($settings["name"]) : ""; ?>'; ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="col-md-2 control-label"><?php echo '<?php echo $this->text("File"); ?>'; ?></label>
        <div class="col-md-6">
          <input type="file">
          <div class="help-block">Example block-level help text here.</div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-6 col-md-offset-2">
          <div class="checkbox">
            <label>
              <input name="settings[status]" type="checkbox"<?php echo '<?php echo empty($settings["status"]) ? "" : " checked"; ?>'; ?>> Check me out
            </label>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-4 col-md-offset-2">
          <div class="btn-toolbar">
            <a href="<?php echo '<?php echo $this->url("admin/module/list"); ?>'; ?>" class="btn btn-default"><?php echo '<?php echo $this->text("Cancel"); ?>'; ?></a>
            <button class="btn btn-default save" name="save" value="1"><?php echo '<?php echo $this->text("Save"); ?>'; ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>