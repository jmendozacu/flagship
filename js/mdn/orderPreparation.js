//************************************************************************************************
//Save shipping informaiton in import tracking page
function submitShippingInformation()
{
	var url = urlSaveShippingInformation;
	var request = new Ajax.Request(
		url,
	    {
	        method:'POST',
	        onSuccess: function onSuccess(transport)
	        			{
	        				elementValues = eval('(' + transport.responseText + ')');
	        				alert(elementValues['message']);
	        			},
	        onFailure: function onFailure() 
	        			{
	        				alert('error');
	        			},
            parameters: Form.serialize(document.getElementById('form_data'))
	    }
    );
}

//************************************************************************************************
//Set preparation warehouse
function changePreparationWarehouse()
{
	var url = urlChangePreparationWarehouse;
	url += 'warehouse_id/' + document.getElementById('preparation_warehouse').value;
	document.location.href = url;
}

//************************************************************************************************
//Set operator
function changeOperator()
{
	var url = urlChangeOperator;
	url += 'user_id/' + document.getElementById('operator').value;
	document.location.href = url;
}