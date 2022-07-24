$(document).ready(function () {

    $('[data-toggle="tooltip"]').tooltip();

    $(".showOnAccountResolve").hide();
    var tenYears = new Date();
    tenYears.setDate(tenYears.getDate() - 3650 );

    let datePicker = $(".datePicker");

    if (datePicker.length){
        datePicker.datepicker({
            yearFirst: true,
            endDate: tenYears.toLocaleDateString()
        });
    }

/*    $(".star_account_number").blur(function () {
        console.log("Blur Called");
        star_account_number = $(this).val();

        resolveUserViaStarPayAccountNumber(star_account_number);
    });*/


    $(".star_account_number").keyup(function () {
        console.log("change called");
        star_account_number = $(this).val();
        resolveUserViaStarPayAccountNumber(star_account_number);
    });



/*
    var userMobileWalletRowSelector = $(".user_mobile_wallet_row");

    if (!userMobileWalletRowSelector.hasClass("x-wallet-type")){
        userMobileWalletRowSelector.hide();
    }
*/

    var userBanksRow = $(".user_banks_row");

    if (!userBanksRow.hasClass("x-wallet-type")){
        userBanksRow.hide();
    }


    $("#country_id").change(function () {

        var selectedCountry = $(this).val();
        var selectedServiceType = $("#service_type").val();

        if (selectedServiceType !== null && selectedServiceType !== ""){

            if (selectedServiceType === "Bank"){
                fetchBankNamesByCountry(selectedCountry);
            }
        }else{
            $("#banks").html("<option>---Select Destination Bank  --</option>");
        }

    });


    $("#service_type").change(function () {

        var selectedCountry = $("#country_id").val();

        console.log("selected country  "+selectedCountry);

        if (selectedCountry !== null && selectedCountry !== ''){
            var selectedServiceType = $(this).val();

            console.log("selected Service Type ",selectedServiceType);
            if (selectedServiceType === "Bank")
            {


                // $("#user_mobile_wallet_row").hide();


                fetchBankNamesByCountry(selectedCountry);

            }else if(selectedServiceType === "Wallet" || selectedServiceType === "Pickup")
            {
                // $("#user_mobile_wallet_row").show();
                $(".user_banks_row").hide();
            }
        }else{
            console.log("selected country is empty ",selectedCountry);
        }
    });


    var input = document.querySelector("#msisdn");
    if (input != null){
        var iti = window.intlTelInput(input,{
            initialCountry: "auto",
            separateDialCode: true,
            hiddenInput: "full",
            autoPlaceholder: "Polite",
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                return "e.g. " + selectedCountryPlaceholder;
            },
            geoIpLookup: function(success, failure) {
                $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    success(countryCode);
                });
            },
            utilsScript: window.location.origin+"/assets/plugins/intl-tel-input-master/build/js/utils.js",
        });

        var defaultMSISDN = window.localStorage.getItem("x-default-msisdn");

        if (defaultMSISDN !== null && defaultMSISDN.length){
            iti.setNumber("+"+defaultMSISDN);
        }
    }

    $("#msisdn").blur(function (event)
    {
        if (iti !== undefined)
            if(iti.isValidNumber()){
                console.log("number valid");
                var numberType = iti.getNumberType();
                if (numberType === intlTelInputUtils.numberType.MOBILE)
                {
                    var selectedCountryData = iti.getSelectedCountryData();
                    console.log(selectedCountryData);
                    console.log(selectedCountryData.dialCode);
                    $("#iso_code").val(selectedCountryData.iso2);
                    $("#dial_code").val(selectedCountryData.dialCode);

                }
            }else{
                console.log("number invalid");
            }

    });


    $("#registrationForm").submit(function (event) {
        if (!validateMSISDNOnFormSubmission(iti)){
            event.preventDefault();
        }
    });

    $("#login_form").submit(function (event) {
        if (!validateMSISDNOnFormSubmission(iti)){
            event.preventDefault();
        }
    });

    $("#airtimeForm").submit(function (event) {

        if (!validateMSISDNOnFormSubmission(iti)){
            event.preventDefault();
        }
    });


    setTimeout(function () {
        drawCallBackHandler();
    },1000);

    $("#beneficiary_id").change(function () {

        var beneficiary_id = $(this).val();
        if (beneficiary_id !== null && beneficiary_id.length){
            //TODO get beneficiary details and rates

            var authUser = JSON.parse(window.localStorage.getItem("x-auth-user"));

            if (authUser.hasOwnProperty('id')){

                $.get(window.location.origin+"/api/resolve-rates/"+authUser.id+"/"+beneficiary_id,function (data) {

                    console.log(data);

                    if (data.hasOwnProperty('rate'))
                    {
                        window.localStorage.setItem("x-rates",data.rate);
                        if (data.hasOwnProperty('destination_currency')){
                            $("#destination_currency_symbol").text(data.destination_currency);
                        }

                        if (data.hasOwnProperty('id')){
                            $("#rate_id").val(data.id);
                        }

                        let sourceAmountSelector = $("#source_amount");
                        let destinationAmountSelector = $ ("#destination_amount");


                        sourceAmountSelector.removeAttr("disabled");
                        destinationAmountSelector.removeAttr("disabled");

                        source_amount =  sourceAmountSelector.val();
                        destination_amount =  destinationAmountSelector.val();

                        if (source_amount !== null && source_amount !== ""){
                            console.log("source amount not empty... calling - calculateReceiveAmountFromSendAmount");
                            calculateReceiveAmountFromSendAmount();
                        }else if (destination_amount !== null && destination_amount !== ""){

                            console.log("destination amount not empty... calling - calculateSendAmountFromReceiveAmount");
                            calculateSendAmountFromReceiveAmount();
                        }


                    }else
                    {
                        sweetAlertDanger("Something Went Wrong... Rate Resolution Failed");
                        disableSendReceiveDetails();
                    }


                });

            }else{
                sweetAlertDanger("Something Went Wrong...");
                disableSendReceiveDetails();
            }

        }else{
            disableSendReceiveDetails();
        }



    });

    $ ("#source_amount").keyup(function () {
        calculateReceiveAmountFromSendAmount();
    });


    $ ("#destination_amount").keyup(function () {
        calculateSendAmountFromReceiveAmount();
    });

    $(".airtimeConfirm").click(function (e) {
        e.preventDefault();
        var formId = $(this).attr("form-id");
        console.log(formId);


        var confirmMessage = $(this).attr("form-alert-message");

        swal({
            title: "Are you sure?",
            text: confirmMessage,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(isConfirm => {
            if (isConfirm) {
                $("#"+formId).submit();
                swal("Submited", "Purchase Request Submitted", "success");
            } else {
                swal("Cancelled", "Request Cancelled", "error");
            }
        });

    });
});

function maskInput(value, pattern) {
    var i = 0,
        v = value.toString();
    return pattern.replace(/#/g, _ => v[i++]);
}

function validateMSISDNOnFormSubmission(iti) {
    var numberType = iti.getNumberType();

    if(!iti.isValidNumber()){
        sweetAlertDanger("Invalid Mobile Number");
        return  false;
    }

    if (numberType !== intlTelInputUtils.numberType.MOBILE) {
        sweetAlertDanger("Only Mobile Numbers Are Allowed");
        return false;
    }
return  true;
}
function drawCallBackHandler() {

    console.log("Draw Call Back Called");
    $('[data-toggle="tooltip"]').tooltip();
    var confirmAction = $(".deleteModel");
    if (confirmAction.length)
    {
        confirmAction.click(function (e) {
            e.preventDefault();
            var formId = $(this).attr("form-id");
            var confirmMessage = $(this).attr("form-alert-message");

            swal({
                title: "Are you sure?",
                text: confirmMessage,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then(isConfirm => {
                if (isConfirm) {
                    $("#"+formId).submit();
                    swal("Submited", "Deletion Request Submitted", "success");
                } else {
                    swal("Cancelled", "Request Cancelled", "error");
                }
            });

        });
    }
}
function calculateReceiveAmountFromSendAmount() {
    rate = window.localStorage.getItem("x-rates");

    if (rate !== null && rate !== "")
    {
        source_amount = $("#source_amount").val();

        if (source_amount !== null && source_amount !== ""){
            amount = Math.floor((Number.parseFloat(source_amount) * Number.parseFloat(rate)) * 100)/100;
            $("#destination_amount").val(amount);
        }else{
            $("#destination_amount").val(0.00);
        }
    }
}
function calculateSendAmountFromReceiveAmount() {
    rate = window.localStorage.getItem("x-rates");

    if (rate !== null && rate !== ""){
        destination_amount = $("#destination_amount").val();

        if (destination_amount !== null && destination_amount !== ""){
            amount = Math.floor((Number.parseFloat(destination_amount) / Number.parseFloat(rate)) * 100)/100;
            $("#source_amount").val(amount);
        }else{
            $("#source_amount").val(0.00);
        }
    }
}
function disableSendReceiveDetails() {
    console.log("disabling send/receive amounts");
    $("#source_amount").val(0.00).attr("disabled","disabled");
    $("#destination_amount").val(0.00).attr("disabled","disabled");
    $("#destination_currency_symbol").text("____");
    window.localStorage.removeItem("x-rates");
}
function sweetAlertDanger(message) {
    swal({
        type: "error",
        title: 'Error',
        text: message,
        confirmButtonClass: "btn-raised btn-danger",
        confirmButtonText: "OK"
    })
}

function sweetAlertSuccess(message) {
    swal({
        type: "success",
        title: 'Success',
        text: message,
        confirmButtonClass: "btn-raised btn-success",
        confirmButtonText: "OK"
    })
}

function sweetAlertInfo(message) {
    swal({
        type: "info",
        title: 'Info',
        text: message,
        confirmButtonClass: "btn-raised btn-info",
        confirmButtonText: "OK"
    })
}

function fetchBankNamesByCountry(selectedCountry) {
    $.get(window.location.origin+"/api/banks/"+selectedCountry,function (data) {
        var bankSelectionInput = "";
        if (data.length){
            for (i = 0; i < data.length; i++){
                bankSelectionInput += "<option value='"+data[i].id+"'>"+data[i].name+"</option>";
            }

            bankSelectionInput = "<option>---Select Destination Bank  --</option>" + bankSelectionInput;

            $("#banks").html(bankSelectionInput);
            $(".user_banks_row").show();
        }
    });
}

function purgeCache() {
window.localStorage.removeItem("x-default-msisdn");
}


function resolveUserViaStarPayAccountNumber(star_account_number) {
    if (star_account_number != null && star_account_number.length > 9){


        let url = window.location.origin+"/api/user/resolve/"+star_account_number+"?api_token="+window.api_token;
        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function(xhr){xhr.setRequestHeader('Authorization', 'Bearer '+window.api_token);},
            success: function(data) {
                console.log(data);
                let showOnAccountResolve = $(".showOnAccountResolve");
                if (data !== null){

                    if (data.hasOwnProperty("user") && data.hasOwnProperty("country") && data.hasOwnProperty("rate")){
                        if (data.hasOwnProperty("user")){

                            var beneficiaryName = "";
                            if (data.user.hasOwnProperty("firstname") && data.user.firstname !== null){
                                beneficiaryName +=data.user.firstname;
                            }

                            if (data.user.hasOwnProperty("lastname") && data.user.lastname !== null){
                                beneficiaryName+= " "+data.user.lastname;
                            }

                            if (data.user.hasOwnProperty("othernames") && data.user.othernames !== null){
                                beneficiaryName+= " "+data.user.othernames;
                            }

                            $("#beneficiary_name").val(beneficiaryName);


                            if (data.user.hasOwnProperty("msisdn") && data.user.msisdn !== null){
                                var msisdn = data.user.msisdn;
                                //TODO mask user msisdn
                                $("#beneficiary_msisdn").val(msisdn);
                            }

                        }

                        if (data.hasOwnProperty("country")){
                            if (data.country.hasOwnProperty("name") && data.country.name !== null){
                                $("#beneficiary_country").val(data.country.name);
                            }

                            if (data.country.hasOwnProperty("currency_code") && data.country.currency_code !== null){
                                $("#destination_currency_symbol").html(data.country.currency_code);
                            }

                        }

                        if (data.hasOwnProperty("rate")){


                            if (data.rate.hasOwnProperty("id") && data.rate.id !== null){
                                $("#rate_id").val(data.rate.id);
                            }


                            if (data.rate.hasOwnProperty("rate") && data.rate.rate !== null){
                                window.localStorage.setItem("x-rates",data.rate.rate);
                            }


                            if (data.rate.hasOwnProperty("destination_currency") && data.rate.destination_currency !== null){
                                $("#destination_currency_symbol").text(data.rate.destination_currency);
                            }


                            let sourceAmountSelector = $("#source_amount");
                            let destinationAmountSelector = $ ("#destination_amount");


                            sourceAmountSelector.removeAttr("disabled");
                            destinationAmountSelector.removeAttr("disabled");

                            source_amount =  sourceAmountSelector.val();
                            destination_amount =  destinationAmountSelector.val();

                            if (source_amount !== null && source_amount !== ""){
                                console.log("source amount not empty... calling - calculateReceiveAmountFromSendAmount");
                                calculateReceiveAmountFromSendAmount();
                            }else if (destination_amount !== null && destination_amount !== ""){

                                console.log("destination amount not empty... calling - calculateSendAmountFromReceiveAmount");
                                calculateSendAmountFromReceiveAmount();
                            }


                        }

                        showOnAccountResolve.show();
                        return;
                    }
                }

                //TODO hide disable inputs

                showOnAccountResolve.hide();
            }
        });

    }
}