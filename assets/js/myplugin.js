//HELPERS

//JQUERY PLUGIN
(function ( $ ) {
    $.fn.serializeObject = function() {

        var data = {};
        $(this).serializeArray().forEach(function(fd, key){
            data[fd.name] = fd.value;
        })      
        return data;
    };


}( jQuery ));


window.$post = function(obj){
    return new Promise((resolve, reject) => {
        $.post(obj).done((result)=>{
            resolve(result);
        });
    });
}

window.arraysMatch = function (arr1, arr2) {

    if (!arr1 || !arr2) {
        console.log("nm 1");
        return false;
    } 

    // Check if the arrays are the same length
    if (arr1.length !== arr2.length) {
        console.log("nm 2");
        return false;
    }

    // Check if all items exist and are in the same order
    for (var i = 0; i < arr1.length; i++) {
        if (arr1[i] !== arr2[i]){
            console.log("nm 3");
            return false;
        } 
    }

    // Otherwise, return true
    return true;

};



window.isEqual = function (value, other) {

    // Get the value type
    var type = Object.prototype.toString.call(value);

    // If the two objects are not the same type, return false
    if (type !== Object.prototype.toString.call(other)) return false;

    // If items are not an object or array, return false
    if (['[object Array]', '[object Object]'].indexOf(type) < 0) return false;

    // Compare the length of the length of the two items
    var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
    var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
    if (valueLen !== otherLen) return false;

    // Compare two items
    var compare = function (item1, item2) {

        // Get the object type
        var itemType = Object.prototype.toString.call(item1);

        // If an object or array, compare recursively
        if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
            if (!isEqual(item1, item2)) return false;
        }

        // Otherwise, do a simple comparison
        else {

            // If the two items are not the same type, return false
            if (itemType !== Object.prototype.toString.call(item2)) return false;

            // Else if it's a function, convert to a string and compare
            // Otherwise, just compare
            if (itemType === '[object Function]') {
                if (item1.toString() !== item2.toString()) return false;
            } else {
                if (item1 !== item2) return false;
            }

        }
    };

    // Compare properties
    if (type === '[object Array]') {
        for (var i = 0; i < valueLen; i++) {
            if (compare(value[i], other[i]) === false) return false;
        }
    } else {
        for (var key in value) {
            if (value.hasOwnProperty(key)) {
                if (compare(value[key], other[key]) === false) return false;
            }
        }
    }

    // If nothing failed, return true
    return true;

};

window.groupBy = function(xs, key) {
  return xs.reduce(function(rv, x) {
    (rv[x[key]] = rv[x[key]] || []).push(x);
    return rv;
  }, {});
};


window.stringIsNumeric = (str)=> {
  if (typeof str != "string") return false // we only process strings!  
  return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
         !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
}

window.parseNumber = (str, precision = 2)=>{

	if(stringIsNumeric(str)){
		str = parseFloat(str);
	}

	if(typeof str == 'number'){
		if(precision)
			return parseFloat(str.toFixed(precision));
		else
			return str;
	}

	return 0;
}

window.unreference = (data)=>{
	return JSON.parse(JSON.stringify(data));
}

window.isArray = (value) => {
    return value && typeof value === 'object' && value.constructor === Array;
}

window.isString = (value) => {
    return typeof value === 'string' || value instanceof String;
}

window.isNumber = (value) => {
    return typeof value === 'number' && isFinite(value);
}

window.isFunction = (value) => {
    return typeof value === 'function';
}

window.isObject = (value) => {
    return value && typeof value === 'object' && value.constructor === Object;
}

window.isNull = (value) => {
    return value === null;
}

window.isBoolean = (value) => {
    return typeof value === 'boolean';
}

window.isRegExp = (value) => {
    return value && typeof value === 'object' && value.constructor === RegExp;
}

window.isError = (value) => {
    return value instanceof Error && typeof value.message !== 'undefined';
}

window.isUndefined = (value) => {
    return typeof value === 'undefined';
}

window.isDate = (value) => {
    return value instanceof Date;
}

window.isSymbol = (value) => {
    return typeof value === 'symbol';
}

window.round = (num, decimals = 2) => {    
    return +(Math.round(num + `e+${decimals}`)  + `e-${decimals}`);
}

window.getDaysInMonth = (month)=>{
    return moment(month, "YYYY-MM").daysInMonth();
}



window.convertModelToFormData = (data = {}, form = null, namespace = '') =>{
    let files = {};
    let model = {};
    for (let propertyName in data) {
        if (data.hasOwnProperty(propertyName) && data[propertyName] instanceof File) {
            files[propertyName] = data[propertyName]
        } else {
            model[propertyName] = data[propertyName]
        }
    }

    model = JSON.parse(JSON.stringify(model))

    let formData = form || new FormData();

    for (let propertyName in model) {

        if (!model.hasOwnProperty(propertyName) 
            //|| !model[propertyName]
        ){
            continue;
        } 

        //console.log(propertyName);

        let formKey = namespace ? `${namespace}[${propertyName}]` : propertyName;
        if (model[propertyName] instanceof Date)
            formData.append(formKey, model[propertyName].toISOString());
        else if (model[propertyName] instanceof File) {
            formData.append(formKey, model[propertyName]);
        }
        else if (model[propertyName] instanceof Array) {
            model[propertyName].forEach((element, index) => {
                const tempFormKey = `${formKey}[${index}]`;
                if (typeof element === 'object') this.convertModelToFormData(element, formData, tempFormKey);
                else formData.append(tempFormKey, element.toString());
            });
        }
        else if (typeof model[propertyName] === 'object' && !(model[propertyName] instanceof File) && model[propertyName])
        {
            this.convertModelToFormData(model[propertyName], formData, formKey);
            console.log(propertyName);
        }else if(model[propertyName] == null){
            formData.append(formKey, '');
        }
        else {
            //console.log(propertyName);
            formData.append(formKey, model[propertyName].toString());
        }
    }

    for (let propertyName in files) {
        if (files.hasOwnProperty(propertyName)) {
            formData.append(propertyName, files[propertyName]);
        }
    }

    return formData;
}



window.pop = {};

pop.alert = (msg, callback = ()=>{})=>{
    bootbox.alert({
        message: `<p class='text-justify text-dark p-0 m-0 ml-2 mr-2'> ${msg}</p>`,
        buttons: {
            ok: {
                label: 'Okay',
                className: 'btn-primary btn-sm'
            },
        },
        callback: (res)=>{
        	callback(res);
        	setTimeout(()=>{
        		$("body").css("padding-right", "0px");
        	}, 500)
        } 
    })

}
pop.confirm = (msg, callback)=>{
	bootbox.confirm({
        message: `<p class='text-justify text-dark p-0 m-0 ml-2 mr-2'> ${msg}</p>`,
        buttons: {
            confirm: {
                label: '<i class="fa fa-check text-light" style="color: #fff"></i> Yes',
                className: 'btn-primary btn-sm mr-2'
            },
            cancel: {
                label: '<i class="fa fa-times "></i> Cancel',
                className: 'btn-secondary btn-sm pull-right'
            }
        },
        callback: (res)=>{
        	callback(res);

        	setTimeout(()=>{
        		$("body").css("padding-right", "0px");
        	}, 500)
        	
        } 
    });
}

pop.loading = (msg)=>{
	/*return bootbox.dialog({
        centerVertical : true,
        message: `<div class="" style="text-align: center;" >
                    <div>
                        <center><img width="50px;" src="${$base_url}img/spinner2.svg"></center>
                    </div>
                    <div class = "loader-text"><center><b>${msg}</b></center></div>  
                </div>`,
        closeButton: false,
        keyboard: false,
        backdrop : 'static',
    }).modal({
        keyboard: false,
        backdrop : 'static'
    });*/

    $('#app-loader-msg').html(msg);
    return $("#app-loader").modal('show');
}


window.toggleShowPassword = () => 
{
    $(".toggle-pass").children().toggleClass('fas fa-eye far fa-eye-slash');
    $(".password").attr('type', $(".password").attr('type') === 'password' ? 'text' : 'password');
}


window.ValidatePassword = () => {
    
    var rules = [{
        Pattern: "[A-Z]",
        Target: "UpperCase"
      },
      {
        Pattern: "[a-z]",
        Target: "LowerCase"
      },
      {
        Pattern: "[0-9]",
        Target: "Numbers"
      },
      {
        Pattern: "[!@@#$%^&_*]",
        Target: "Symbols"
      }
    ];
  
    //Just grab the password once
    var password = $(".password").val();
  
    $("#Length").removeClass(password.length > 7  ? "pattern-error" : "pattern-ok");
    $("#Length").addClass(password.length > 7 ? "pattern-ok" : "pattern-error");
    
    /*Iterate our remaining rules. The logic is the same as for Length*/
    for (var i = 0; i < rules.length; i++) {
  
      $("#" + rules[i].Target).removeClass(new RegExp(rules[i].Pattern).test(password) ? "pattern-error" : "pattern-ok"); 
      $("#" + rules[i].Target).addClass(new RegExp(rules[i].Pattern).test(password) ? "pattern-ok" : "pattern-error");
    }
    // Reference
    // https://stackoverflow.com/questions/52069794/how-to-check-password-validation-dynamically-using-jquery-javascript
}


$(document).ready(function() {
    $(".password").on('keyup', ValidatePassword)
    $(".toggle-pass").on('click', toggleShowPassword)
});