//vars
var KC_catchKeys = false;
var KC_displayInput = null;
var KC_value = '';
var KC_onEnter = null;

//***************************************************************************************************
//Catch keys
function enableCatchKeys(edisplayInput, eOnEnter)
{
    KC_catchKeys = true;
    KC_displayInput = edisplayInput;
    KC_onEnter = eOnEnter;
}

//****************************************************************************************************
//disable handle key
function disableCatchKeys()
{
    KC_catchKeys = false;
}

//****************************************************************************************************
//Raised when key is typed
function handleKey(evt) {

    if (!KC_catchKeys)
        return true;

    //Dont process event if focuses control is text
    if (document.activeElement)
    {
        if ((document.activeElement.type == 'text') || (document.activeElement.tagName.toLowerCase() == 'textarea'))
            return true;
    }

    var evt = (window.event ? window.event : evt);
    var keyCode;

    //if not ie
    if (navigator.appName != 'Microsoft Internet Explorer')
        keyCode = evt.which;
    else
        keyCode = evt.keyCode;

    if (keyCode != 13)
    {
        KC_value += String.fromCharCode(keyCode);
        if (KC_displayInput != null)
            KC_displayInput.value = KC_value;
        if (document.getElementById('scanner_value'))
            document.getElementById('scanner_value').innerHTML = KC_value;
    }
    else
    {
        eval(KC_onEnter);
        KC_value = '';
    }

    return false;
}