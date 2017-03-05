<?php
/**
 * @package Skeleton module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
?>
<form method="post" class="form-horizontal skeleton">
  <input type="hidden" name="token" value="<?php echo $this->prop('token'); ?>">
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading"><?php echo $this->text('Basic'); ?></div>
        <div class="panel-body">
          <div class="form-group required<?php echo $this->error('module.id', ' has-error'); ?>">
            <label class="col-md-3 control-label">
                <?php echo $this->text('ID'); ?>
            </label>
            <div class="col-md-9">
              <input maxlength="255" name="skeleton[module][id]" class="form-control" value="<?php echo isset($skeleton['module']['id']) ? $this->escape($skeleton['module']['id']) : ''; ?>">
              <div class="help-block">
                  <?php echo $this->error('module.id'); ?>
                <div class="text-muted">
                  <?php echo $this->text('A unique module ID. Use latin lowercase letters. Underscores and digits are allowed too, but not at the beginning'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group required<?php echo $this->error('module.version', ' has-error'); ?>">
            <label class="col-md-3 control-label">
              <?php echo $this->text('Version'); ?>
            </label>
            <div class="col-md-9">
              <input name="skeleton[module][version]" class="form-control" value="<?php echo isset($skeleton['module']['version']) ? $this->escape($skeleton['module']['version']) : '1.0.0-alfa.1'; ?>">
              <div class="help-block">
                  <?php echo $this->error('module.version'); ?>
                <div class="text-muted">
                  <?php echo $this->text('A version number. Enter whatever you want, but it\'s strongly recommended to follow <a href="@url">semantic versioning</a> guidelines', array('@url' => 'http://semver.org')); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group required<?php echo $this->error('module.core', ' has-error'); ?>">
            <label class="col-md-3 control-label"><?php echo $this->text('Core'); ?></label>
            <div class="col-md-9">
              <input name="skeleton[module][core]" class="form-control" value="<?php echo isset($skeleton['module']['core']) ? $this->escape($skeleton['module']['core']) : strtok(GC_VERSION, '.') . '.x'; ?>">
              <div class="help-block">
                  <?php echo $this->error('module.core'); ?>
                <div class="text-muted">
                  <?php echo $this->text('GPL Cart core compatibility. For example, if the module requires any version of 1.x branch, enter "1.x"'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group required<?php echo $this->error('module.author', ' has-error'); ?>">
            <label class="col-md-3 control-label"><?php echo $this->text('Author'); ?></label>
            <div class="col-md-9">
              <input maxlength="255" name="skeleton[module][author]" class="form-control" value="<?php echo isset($skeleton['module']['author']) ? $this->escape($skeleton['module']['author']) : $this->escape($author); ?>">
              <div class="help-block">
                  <?php echo $this->error('module.author'); ?>
                <div class="text-muted">
                  <?php echo $this->text('An author name to be shown in the module info and DocBlocks'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group<?php echo $this->error('module.name', ' has-error'); ?>">
            <label class="col-md-3 control-label">
              <?php echo $this->text('Name'); ?>
            </label>
            <div class="col-md-9">
              <input maxlength="255" name="skeleton[module][name]" class="form-control" value="<?php echo isset($skeleton['module']['name']) ? $this->escape($skeleton['module']['name']) : ''; ?>">
              <div class="help-block">
                  <?php echo $this->error('module.name'); ?>
                <div class="text-muted">
                  <?php echo $this->text('A short human name for the module'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group<?php echo $this->error('module.description', ' has-error'); ?>">
            <label class="col-md-3 control-label">
              <?php echo $this->text('Description'); ?>
            </label>
            <div class="col-md-9">
              <textarea name="skeleton[module][description]" class="form-control"><?php echo isset($skeleton['module']['description']) ? $this->escape($skeleton['module']['description']) : ''; ?></textarea>
              <div class="help-block">
                  <?php echo $this->error('module.description'); ?>
                <div class="text-muted">
                  <?php echo $this->text('A descriptive text that explains the purpose of the module'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-3 control-label"><?php echo $this->text('License'); ?></label>
            <div class="col-md-9">
              <select name="skeleton[module][license]" class="form-control">
                <?php foreach ($licenses as $name => $url) { ?>
                <option value="<?php echo $name; ?>"<?php echo isset($skeleton['module']['license']) && $skeleton['module']['license'] == $name ? ' selected' : ''; ?>><?php echo $this->escape($name); ?></option>
                <?php } ?>
              </select>
              <div class="help-block">
                <?php echo $this->text('Select a license to be shown in the module info and DocBlocks'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading"><?php echo $this->text('Hooks'); ?></div>
        <div class="panel-body">
          <p>
            <?php echo $this->text('Select scopes you want to hook into. If selected, all available hooks for the scope will be extracted from the source files and corresponding methods created in the module class'); ?>
          </p>
          <div class="row">
            <div class="col-md-6">
              <div class="checkbox">
                <label>
                  <input type="checkbox" onclick="$('[name=\'skeleton[hooks][]\']').prop('checked', $(this).prop('checked'));"> <?php echo $this->text('All'); ?>
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <?php foreach ($hooks as $chunk) { ?>
            <div class="col-md-6">
              <?php foreach ($chunk as $group => $name) { ?>
              <div class="checkbox">
                <label>
                  <input type="checkbox" value="<?php echo $group; ?>" name="skeleton[hooks][]"<?php echo isset($skeleton['hooks']) && in_array($group, $skeleton['hooks']) ? ' checked' : ''; ?>> <?php echo $this->escape($name); ?>
                </label>
              </div>
              <?php } ?>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading"><?php echo $this->text('Structure'); ?></div>
        <div class="panel-body">
          <p><?php echo $this->text('Extra options to setup your module the best way. Although GPL Cart has no strict requirements on how to organize your classes, it\'s recommended to follow common rules'); ?></p>
          <div class="checkbox">
            <label>
              <input type="checkbox" onclick="$('[name=\'skeleton[structure][]\']').prop('checked', $(this).prop('checked'));"> <?php echo $this->text('All'); ?>
            </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="controller" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('controller', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use controllers'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Controllers provide callbacks for URL routes and interact with browser or external resources'); ?></div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="helper" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('helper', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use helpers'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Helper classes provide independent reusable methods to be used everywhere'); ?></div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="handler" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('handler', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use handlers'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Handler classes are defined in hooks and focused on certain tasks, e.g validating'); ?></div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="template" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('template', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use templates'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Templates display various data from controllers to users'); ?></div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="model" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('model', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use models'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Models provide a business logic and can interact with database'); ?></div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="asset" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('asset', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use assets'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Assets are JS/CSS files and images. Required for theme modules'); ?></div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" value="override" name="skeleton[structure][]"<?php echo isset($skeleton['structure']) && in_array('override', $skeleton['structure']) ? ' checked' : ''; ?>> <?php echo $this->text('Use overrides'); ?>
            </label>
            <div class="help-block"><?php echo $this->text('Overriding allows developers to adjust core methods without touching source files'); ?></div>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <button class="btn btn-default" name="create" value="1"><?php echo $this->text('Create'); ?></button>
        </div>
      </div>
    </div>
  </div>
</form>
<?php if (!empty($job)) { ?>
    <?php echo $job; ?>
<?php } ?>
