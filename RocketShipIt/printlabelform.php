<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>
<?php
/**
 * Create account form template
 *
 * @see app/design/frontend/base/default/template/customer/form/register.phtml
 */
/** @var $this Mage_Customer_Block_Form_Register */
?>
<div class="account-create">
    <div class="page-title">
        <h1><?php echo 'Create a Label'; ?></h1>
    </div>
    <div class="error-msg">
    </div>

    <form action="printlabel.php" method="post" id="form-validate" class="scaffold-form" enctype="multipart/form-data">
        <div class="fieldset">
            <ul>
                <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Name'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toName" value=""  id="toName" class="input-text " />
                        </div>
                    </div>
                </li>
                 <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Company'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toCompany" value=""  id="toCompany" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Phone'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toPhone" value=""  id="toPhone" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Address'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toAddr1" value=""  id="toAddr1" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'City'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toCity" value=""  id="toCity" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'State'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toState" value=""  id="toState" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Zip Code'; ?></label>
                        <div class="input-box">
                            <input type="text" name="toCode" value=""  id="toCode" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Package Length'; ?></label>
                        <div class="input-box">
                            <input type="text" name="length" value=""  id="toCompany" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Package Width'; ?></label>
                        <div class="input-box">
                            <input type="text" name="width" value=""  id="width" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Package Height'; ?></label>
                        <div class="input-box">
                            <input type="text" name="height" value=""  id="height" class="input-text " />
                        </div>
                    </div>
                </li>
                   <li class="fields">
                    <div class="field">
                        <label for="toName" class="required"><em>*</em><?php echo 'Package Weight'; ?></label>
                        <div class="input-box">
                            <input type="text" name="weight" value=""  id="weight" class="input-text " />
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="buttons-set">
            <button type="submit" title="<?php echo "Create Label";?>" class="button"><span><span><?php echo "Create Label";?></span></span></button>
        </div>
    </form>
</div>
