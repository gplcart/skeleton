/**
* @package <?php echo $module['name']; ?> 
* @author <?php echo $module['author']; ?> 
* @author Skeleton https://github.com/gplcart/skeleton 
* @copyright Copyright (c) <?php echo date('Y'); ?>, <?php echo $module['author']; ?> 
* @license <?php echo $module['license_url']; ?> 
*/
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php //echo $this->prop('token');// Required, uncomment!!  ?>">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-group">
        <label class="col-md-2 control-label">Status</label>
        <div class="col-md-6">
          <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default<?php echo empty($settings['status']) ? '' : ' active'; ?>">
              <input name="settings[status]" type="radio" autocomplete="off" value="1"<?php echo empty($settings['status']) ? '' : ' checked'; ?>>
              Enabled
            </label>
            <label class="btn btn-default<?php echo empty($settings['status']) ? ' active' : ''; ?>">
              <input name="settings[status]" type="radio" autocomplete="off" value="0"<?php echo empty($settings['status']) ? ' checked' : ''; ?>>
              Disabled
            </label>
          </div>
          <div class="help-block">
            <?php //echo $this->text('Status'); //Wrap translatable strings in $this->text() ?>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-4 col-md-offset-2">
          <div class="btn-toolbar">
            <a href="<?php //echo $this->url('admin/module/list');  ?>" class="btn btn-default"><i class="fa fa-reply"></i> Cancel</a>
            <button class="btn btn-default save" name="save" value="1">
              <i class="fa fa-floppy-o"></i> Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>