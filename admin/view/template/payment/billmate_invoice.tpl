<?php if(version_compare(VERSION,'2.0.0','>=')): ?>
<?php echo $header; ?><?php echo $column_left; ?>
<?php else: ?>
<?php echo $header; ?>
<?php endif; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
    <?php if($latest_release != ''){ ?>
    <div class="warning"><?php echo $latest_release; ?></div>
    <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
    <?php if ($error_credentials) { ?>
    <div class="warning"><?php echo $error_credentials; ?></div>
    <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="htabs" class="htabs"><a href="#tab-general"><?php echo $tab_general ?></a><a href="#tab-log"><?php echo $tab_log ?></a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general"><img src="<?php echo HTTP_CATALOG; ?>billmate/images/bm_faktura_l.png" style="float:right" />
          <div id="vtabs" class="vtabs">
            <?php foreach ($countries as $country) { ?>
            <a href="#tab-<?php echo $country['code']; ?>"><?php echo $country['name']; ?></a>
            <?php } ?>
          </div>
          <?php foreach ($countries as $country) { ?>
          <div id="tab-<?php echo $country['code']; ?>" class="vtabs-content">
            <table class="form">
              <tr>
                <td><?php echo $entry_merchant.'<br /><span class="help">'.$entry_merchant_help; ?></span></td>
                <td><input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][merchant]" value="<?php echo isset($billmate_invoice[$country['code']]) ? $billmate_invoice[$country['code']]['merchant'] : ''; ?>" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_secret.'<br /><span class="help">'.$entry_secret_help; ?>; ?></td>
                <td><input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][secret]" value="<?php echo isset($billmate_invoice[$country['code']]) ? $billmate_invoice[$country['code']]['secret'] : ''; ?>" /></td>
              </tr>
              <tr>
                <td valign="top"><?php echo $entry_description; ?></td>
                <td><textarea cols="84" rows="10" name="billmate_invoice[<?php echo $country['code']; ?>][description]"><?php echo isset($billmate_invoice[$country['code']]['description']) ? $billmate_invoice[$country['code']]['description'] : ''; ?></textarea></td>
              </tr>
              <tr>
                <td><?php echo $entry_server; ?></td>
                <td><select name="billmate_invoice[<?php echo $country['code']; ?>][server]">
                    <?php if (isset($billmate_invoice[$country['code']]) && $billmate_invoice[$country['code']]['server'] == 'live') { ?>
                    <option value="live" selected="selected"><?php echo $text_live; ?></option>
                    <?php } else { ?>
                    <option value="live"><?php echo $text_live; ?></option>
                    <?php } ?>
                    <?php if (isset($billmate_invoice[$country['code']]) && $billmate_invoice[$country['code']]['server'] == 'beta') { ?>
                    <option value="beta" selected="selected"><?php echo $text_beta; ?></option>
                    <?php } else { ?>
                    <option value="beta"><?php echo $text_beta; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $entry_mintotal.'<br /><span class="help">'.$entry_mintotal_help; ?></span></td>
                <td><input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][mintotal]" value="<?php echo isset($billmate_invoice[$country['code']]) ? $billmate_invoice[$country['code']]['mintotal'] : ''; ?>" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_maxtotal.'<br /><span class="help">'.$entry_maxtotal_help; ?></span></td>
                <td><input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][maxtotal]" value="<?php echo isset($billmate_invoice[$country['code']]) ? $billmate_invoice[$country['code']]['maxtotal'] : ''; ?>" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_pending_status; ?></td>
                <td><select name="billmate_invoice[<?php echo $country['code']; ?>][pending_status_id]">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if (isset($billmate_invoice[$country['code']]) && $order_status['order_status_id'] == $billmate_invoice[$country['code']]['pending_status_id']) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $entry_accepted_status; ?></td>
                <td><select name="billmate_invoice[<?php echo $country['code']; ?>][accepted_status_id]">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if (isset($billmate_invoice[$country['code']]) && $order_status['order_status_id'] == $billmate_invoice[$country['code']]['accepted_status_id']) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>

                <tr>
                    <td><?php echo $entry_available_countries; ?></td>
                    <td><input type="text" name="billmateinvoice-country" value="" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><div id="billmate-country" class="scrollbox">
                            <?php $class = 'odd'; ?>
                            <?php if(isset($billmate_country) && is_array($billmate_country)){ ?>
                            <?php foreach ($billmate_country as $key => $b_country) { ?>
                            <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                            <div id="billmate-country<?php echo $key; ?>" class="<?php echo $class; ?>"><?php echo $b_country['name']; ?><img src="view/image/delete.png" alt="" />
                                <input type="hidden" name="billmateinvoice-country[<?php echo $key;?>][name];?>" value="<?php echo $b_country['name']; ?>" />

                            </div>
                            <?php } ?>
                            <?php } ?>
                        </div></td>
                </tr>
                <script type="text/javascript">
                    var token = '<?php echo $token; ?>';
                </script>
                <?php if(version_compare(VERSION,'2.0.0','>=')): ?>
                    <script src="/billmate/js/billmate.js"></script>
                <?php else: ?>
                    <script src="/billmate/js/legacy-billmate.js"></script>
                <?php endif; ?>
                <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="billmate_invoice[<?php echo $country['code']; ?>][status]">
                    <?php if (isset($billmate_invoice[$country['code']]) && $billmate_invoice[$country['code']]['status']) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $entry_sort_order ?></td>
                <td>
				<input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][sort_order]" value="<?php echo isset($billmate_invoice[$country['code']]) ? $billmate_invoice[$country['code']]['sort_order'] : ''; ?>" /></td>
              </tr>
            </table>
          </div>
          <?php } ?>
        </div>
        <div id="tab-log">
          <table class="form">
            <tr>
              <td><textarea wrap="off" style="width: 98%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;"><?php echo $log ?></textarea></td>
            </tr>
            <tr>
              <td style="text-align: right;"><a href="<?php echo $clear; ?>" class="button"><?php echo $button_clear ?></a></td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#htabs a').tabs();
$('#vtabs a').tabs();
//--></script> 
<?php echo $footer; ?> 
