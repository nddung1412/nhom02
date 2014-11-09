<style type="text/css">
.magic_tabs { margin: 0; padding: 0; float: left; width: 132px; position: relative; z-index: 1}
.magic_tabs li { list-style-type: none; margin: 0; padding: 0; }
.magic_tabs a { display: inline-block; width: 120px; margin-bottom: -1px; border: 1px solid #CC9933; color: #CC9933; font-weight: bold; /*line-height: 22px;*/ margin-right: -1px; padding: 7px 5px; text-decoration: none; background: #f2f2f2; font-size: 12px; }
.magic_tabs a.tabactive { text-decoration: none; color: #000; font-weight: bold; background: #fff; border-right: none; width: 121px;}
.magic_tabs li a:hover { background: #CC9933; color: #fff; text-decoration: none; border-right: none; }
.magic_tabs li a.tabactive:hover { background: #fff; color: #000; }
.magic_tabCont { display: none; border: 1px solid #CC9933; padding: 10px; }
.magic_tabContWrapper { float: left; width: 797px; clear: right; margin: 0; margin-left: -1px;}
.magic_tabCont.magic_active { display: block; }
.magic_tabCont div.margin-form label { padding: 0 4px; }
.magic_tabCont label { padding: 0; width: 200px; }
.magic_tabCont .margin-form label { padding: 0.2em 0 0; margin-right: 10px; width: auto; }
.magic_tabCont .margin-form { padding-top: 1px !important; padding-left: 210px !important; }
.magic_hint {
    display: block;
    position: relative;
    left: 0px;
    width: 446px;
    margin-top: 4px;
    margin-bottom: 2px;
    border: 1px solid #c93;
    padding: 6px;
    color: #7F7F7F;
    background-color: #ffc;
}
.margin-form input[type=text] {
    width: 450px;
}
<?php if($this->isPrestahop15x) { ?>
.magic_tabContWrapper {
    width: 835px;
}
#magiczoomplus_settings, h2#title {
    margin: 0 auto;
    width: 985px;
}
h2#title {
    text-align: center;
}
<?php } ?>
#magiczoomplus_settings > p {
    margin: 0;
    padding-top: 6px;
    clear: left;
}
#magiczoomplus_images {
    border-bottom: none;
}
#magiczoomplus_images tr td input[type="text"] {
    width: 133px;
    margin: 5px 0;
}
#magiczoomplus_images tr td textarea {
    width: 133px;
    min-width: 133px;
    max-width: 133px;
    margin: 5px 0;
}
#magiczoomplus_images tr td select {
    width: 38px;
}
#magiczoomplus_upload_list {
    border-bottom: none;
}
#magiczoomplus_upload_list tr td:first-child {
   width: 180px;
}
#magiczoomplus_upload_list tr td input[type="text"], #magiczoomplus_upload_list tr td textarea {
    margin: 5px 0;
}
.magic_tabCont fieldset { margin: 17px 0px; }
#tutorialLink {
    float: right;
    margin-right: 10px;
    color: #268CCD;
    font-weight: bold;
    text-decoration: underline;
}
#magicscroll_icon {
    float: left;
    margin: 0 5px 5px 0;
}
</style>
<h2 id="title">Magic Zoom Plus configuration</h2>
<form id="magiczoomplus_settings" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
<p style="text-align: center;">
<input class="button" type="button" value="<?php echo $this->l('Save settings'); ?>" onclick="document.getElementById('magic_submit').click();" />
&nbsp;<a id='tutorialLink' target='_blank' title='Play tutorial' href='http://www.youtube.com/watch?v=yAix6lXqyAw&t=0m54s'>Play tutorial</a>
</p>
    <ul class="magic_tabs">
        <?php foreach($this->getBlocks() as $blockId => $blockTitle) { ?>
        <li><a id="<?php echo $blockId; ?>" onclick="return magic_changeTab(this);" <?php if($blockId == 'default') echo 'class="tabactive" '; ?>href="#"><?php echo $blockTitle; ?></a></li>
        <?php } ?>
    </ul>
    <div id="mstabs" class="magic_tabContWrapper">
    <?php foreach($paramsMap as $blockId => $groups) {
        //get params from DB
        $tool = $this->loadTool($blockId);
    ?>
    <div id="<?php echo $blockId; ?>_content" class="magic_tabCont<?php if($blockId == 'default') echo ' magic_active'; ?>">
        <?php foreach($groups as $groupTitle => $params) { ?>
        <fieldset>
        <legend><img alt="" src="../img/admin/contact.gif" /><?php echo $groupTitle; ?></legend>
            <?php foreach($params as $id) {
                $param_value = $tool->params->getValue($id);
                $param_enable = ($blockId == 'default' || $id == 'enable-effect' || $id == 'max-number-of-products' || $tool->params->paramExists($id));
                if($tool->type == 'standard' && in_array($groupTitle, array('General', 'Scroll', 'Scroll Arrows', 'Scroll Slider', 'Scroll effect', 'Multiple images'))) {
                    $param_enable = true;
                }
            ?>
            <label for="<?php echo $blockId.'-'.$id; ?>"><?php echo $tool->params->getLabel($id); ?></label>
            <div class="margin-form">
            <?php
            if($id == 'magicscroll') {
                echo "<img id=\"magicscroll_icon\" src=\""._MODULE_DIR_."magiczoomplus/admin_graphics/magicscroll.png\" />";
            }
            switch($tool->params->getType($id)) {
                case "array":
                    if($tool->params->getSubType($id, $tool->params->generalProfile) == 'radio') {
                        foreach($tool->params->getValues($id) as $p) {
                            if($blockId != 'product' && $id == 'enable-effect' && $p == 'Swap images only') continue;
                            ?><input type="radio" value="<?php echo $p; ?>"<?php echo (($param_value == $p)?' checked="checked"':''); ?><?php echo $param_enable?'':' disabled="disabled"'; ?> name="<?php echo $blockId.'-'.$id; ?>" id="<?php echo $blockId.'-'.$id.'-'.$p; ?>" /><?php
                            ?><label class="t" for="<?php echo $blockId.'-'.$id.'-'.$p; ?>"><?php
                            $pLower = strtolower($p);
                            if($pLower == "yes")
                                echo '<img src="../modules/magiczoomplus/admin_graphics/yes.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />';
                            elseif($pLower == "no")
                                echo '<img src="../modules/magiczoomplus/admin_graphics/no.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />';
                            elseif($pLower == "left")
                                echo '<img src="../modules/magiczoomplus/admin_graphics/left.gif" alt="'.$this->l('Left').'" title="'.$this->l('Left').'" />';
                            elseif($pLower == "right")
                                echo '<img src="../modules/magiczoomplus/admin_graphics/right.gif" alt="'.$this->l('Right').'" title="'.$this->l('Right').'" />';
                            elseif($pLower == "top")
                                echo '<img src="../modules/magiczoomplus/admin_graphics/top.gif" alt="'.$this->l('Top').'" title="'.$this->l('Top').'" />';
                            elseif($pLower == "bottom")
                                echo '<img src="../modules/magiczoomplus/admin_graphics/bottom.gif" alt="'.$this->l('Bottom').'" title="'.$this->l('Bottom').'" />';
                            elseif($id == 'enable-effect' && ($pLower == "zoom" || $pLower == "expand" || $pLower == "swap images only")) {
                                switch($pLower) {
                                    case 'zoom':
                                    case 'expand':
                                        $alt = $title = $this->l($p.' only');
                                        $label = $p.' only';
                                        break;
                                    case 'swap images only':
                                        $alt = $title = $this->l($p);
                                        $label = 'Swap images';
                                        break;
                                }
                                echo '<img src="../modules/magiczoomplus/admin_graphics/yes.gif" alt="'.$alt.'" title="'.$title.'" />'.$label;
                            }
                            else echo $p;
                            ?></label><?php
                        }
                    } elseif($tool->params->getSubType($id, $tool->params->generalProfile) == 'select') {
                        ?><select name="<?php echo $blockId.'-'.$id; ?>" id="<?php echo $blockId.'-'.$id; ?>"<?php echo $param_enable?'':' disabled="disabled"'; ?>><?php
                        foreach($tool->params->getValues($id) as $p) {
                            ?><option value="<?php echo $p; ?>"<?php echo (($param_value==$p)?' selected="selected"':''); ?>><?php echo $p; ?></option><?php
                        }
                        ?></select><?php
                    } else {
                        ?><input type="text" name="<?php echo $blockId.'-'.$id; ?>" id="<?php echo $blockId.'-'.$id; ?>" value="<?php echo $param_value; ?>"<?php echo $param_enable?'':' disabled="disabled"'; ?> /><?php
                    }
                    break;
                case "num":
                case "text":
                default:
                    ?><input type="text" name="<?php echo $blockId.'-'.$id; ?>" id="<?php echo $blockId.'-'.$id; ?>" value="<?php echo $param_value; ?>"<?php echo $param_enable?'':' disabled="disabled"'; ?> /><?php
            }
            if($blockId != 'default' && $id != 'enable-effect' && $id != 'max-number-of-products' && !($tool->type == 'standard' && in_array($groupTitle, array('General', 'Scroll', 'Scroll Arrows', 'Scroll Slider', 'Scroll effect', 'Multiple images')))) {
                if($param_enable) {
                    echo '&nbsp;&nbsp;<a href="#" onclick="return useDefaultOption(this, \''.$id.'\', \''.$blockId.'\');">'.$this->l('use default option').'</a>';
                } else {
                    echo '&nbsp;&nbsp;<a href="#" onclick="return useDefaultOption(this, \''.$id.'\', \''.$blockId.'\');" class="optionDisabled">'.$this->l('edit').'</a>';
                }
            }
            $hint = '';
            if($tool->params->getDescription($id))
                $hint = $tool->params->getDescription($id);
            if($tool->params->getType($id) != "array" && $tool->params->valuesExists($id, '', false)) {
                if($hint != '') $hint .= "<br />";
                $hint .= "#allowed values: ".implode(", ",$tool->params->getValues($id));
            }
            if($hint != '') {
                ?><p class="magic_hint clear"><?php echo $hint; ?></p><?php
            }
            ?>
            </div>
            <div class="clear pspace"></div>
            <?php } ?>
        </fieldset><br />
        <?php } ?>
    </div>
    <?php } ?>
    </div>
<input type="hidden" id="magic_tabs_current" name="magic_tabs_current" value="default" />
<script type="text/javascript">
    //<![CDATA[
    var magic_tabs_current = 'default';
    function magic_changeTab(elm) {
        if(document.getElementById(magic_tabs_current)) {
            document.getElementById(magic_tabs_current).className = '';
            document.getElementById(magic_tabs_current+'_content').className = 'magic_tabCont';
        }
        magic_tabs_current = document.getElementById('magic_tabs_current').value = elm.id;
        document.getElementById(magic_tabs_current).className = 'tabactive';
        document.getElementById(magic_tabs_current+'_content').className = 'magic_tabCont magic_active';
        return false;
    }
<?php
    $magicTabsCurrent = Tools::getValue('magic_tabs_current', '');
    if(!empty($magicTabsCurrent)) {
?>
    magic_changeTab(document.getElementById('<?php echo $magicTabsCurrent; ?>'));
<?php
    }
?>
    //]]>
</script>
<p style="text-align: center;"><input class="button" type="submit" id="magic_submit" name="magic_submit" value="<?php echo $this->l('Save settings'); ?>" /></p>
<script src="<?php echo _MODULE_DIR_ ?>magiczoomplus/options.js" type="text/javascript"></script>
<script type="text/javascript">
    //<![CDATA[
    var blocks = ["<?php echo implode('", "', array_keys($this->getBlocks())); ?>"];

    function useDefaultOption(anchorEl, optionId, blockId) {
        if($(anchorEl).hasClass('optionDisabled')) {
            $(anchorEl).removeClass('optionDisabled').html('<?php echo $this->l('use default option'); ?>').
            parent().find("select, input").removeAttr("disabled");
        } else {
            $(anchorEl).addClass('optionDisabled').html('<?php echo $this->l('edit'); ?>');
            $(anchorEl.parentNode).find("select, input[type='text']").attr("disabled", true).val($("#default-" + optionId).val());
            $(anchorEl.parentNode).find("input[type='radio']").attr("disabled", true).val([$("input[name='default-" + optionId + "']:checked").val()]);
        }
        return false;
    }

    $("#default_content").find("select, input[type='text']").bind("change", function(){
        var optionVal = $(this).val();
        var option = this.id.replace("default-","");
        for(block in blocks) {
            if(blocks[block] == 'default') continue;
            $("#" + blocks[block] + "-" + option + ":disabled").val(optionVal);
        }
    }).end().find("input[type=\'radio\']").bind("change", function(){
        var optionVal = $(this).val();
        var option = this.id.replace("default-","").replace("-" + optionVal,"");
        for(block in blocks) {
            if(blocks[block] == 'default') continue;
            $("input[name='" + blocks[block] + "-" + option + "']:disabled").val([optionVal]);
        }
    });

    initOptionsValidation('product-template', 'product-magicscroll');

    //]]>
</script>
</form>
