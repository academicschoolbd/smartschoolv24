<?php
/**
 * Quick Admission form.
 *
 * Minimal admission UI — only Class, Section, Roll, Full Name are asked here.
 * Register no., username, password and every other student field are populated
 * server-side with safe defaults; they can be edited from the regular student
 * profile screen afterwards.
 */
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <section class="panel">
            <header class="panel-heading">
                <h4 class="panel-title">
                    <i class="far fa-edit"></i> <?php echo translate('quick_admission'); ?>
                </h4>
            </header>
            <?php echo form_open(base_url('student/quick_add'), array('class' => 'form-horizontal')); ?>
                <div class="panel-body">

                    <div class="alert alert-info mb-md">
                        <i class="fas fa-info-circle"></i>
                        <?php echo translate('quick_admission_help'); ?>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo translate('class'); ?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <?php
                                $arrayClass = $this->app_lib->getClass($branch_id);
                                echo form_dropdown(
                                    'class_id',
                                    $arrayClass,
                                    set_value('class_id'),
                                    "class='form-control' id='class_id' onchange='getSectionByClass(this.value,1)' required data-plugin-selectTwo data-width='100%'"
                                );
                            ?>
                            <span class="error"><?php echo form_error('class_id'); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo translate('section'); ?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <?php
                                $arraySection = $this->app_lib->getSections(set_value('class_id'), true);
                                echo form_dropdown(
                                    'section_id',
                                    $arraySection,
                                    set_value('section_id'),
                                    "class='form-control' id='section_id' required data-plugin-selectTwo data-width='100%'"
                                );
                            ?>
                            <span class="error"><?php echo form_error('section_id'); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo translate('roll'); ?></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="roll" value="<?php echo set_value('roll'); ?>" placeholder="<?php echo translate('roll'); ?>" />
                            <span class="error"><?php echo form_error('roll'); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo translate('first_name'); ?> &amp; <?php echo translate('last_name'); ?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="full_name" value="<?php echo set_value('full_name'); ?>" placeholder="<?php echo translate('first_name') . ' ' . translate('last_name'); ?>" />
                            <span class="error"><?php echo form_error('full_name'); ?></span>
                        </div>
                    </div>

                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-7">
                            <button class="btn btn-default" type="submit" name="submit" value="quick_save">
                                <i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
                            </button>
                            <a class="btn btn-default" href="<?php echo base_url('student/view'); ?>">
                                <?php echo translate('cancel'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </section>
    </div>
</div>
