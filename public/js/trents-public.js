const trents = new Vue({
    el: '#rents_wrap',
    data: {
        isDisabled: false,
        progress_page: false,
        activeJob_page: false,
        createJob_page: false,
        trip_history_page: false,
        payments_page: false,
        profile_page: false,
        pageTitle: 'চলমান ট্রিপ্স',
        driverDocs: {
            documentType: 'nid-card',
            isNidCard: true,
            isPassport: false,
            isDriving: false
        },
        cancelJob: {
            isCancelJobForm: false,
            application_id: null,
            isApprovalForm: false,
            cancelReason: null
        },
        paymentTrip: "",     
        currentDue: 0,   
    },
    methods: {
        allMenusDisable: function () {
            this.progress_page = false;
            this.activeJob_page = false;
            this.createJob_page = false;
            this.trip_history_page = false;
            this.payments_page = false;
            this.profile_page = false;
        },
        change_url: function (url) {
            history.pushState('', '', url)
        },
        progress_menu: function () {
            // disable all menus
            this.allMenusDisable();
            this.progress_page = true;
            this.pageTitle = "আবেদন সমূহ";
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=progress');
        },
        activeJob_menu: function () {
            // disable all menus
            this.allMenusDisable();
            this.activeJob_page = true;
            this.pageTitle = "চলমান ট্রিপ্স";
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=active');
        },
        createJob_menu: function () {
            // disable all menus
            this.allMenusDisable();
            this.createJob_page = true;
            this.pageTitle = "নতুন ট্রিপ যুক্ত";
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=newjob');
        },
        triphistory_menu: function () {
            // disable all menus
            this.allMenusDisable();
            this.trip_history_page = true;
            this.pageTitle = "ট্রিপ হিস্টোরি";
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=triphistory');
        },
        payments_menu: function () {
            // disable all menus
            this.allMenusDisable();
            this.payments_page = true;
            this.pageTitle = "পেমেন্ট হিস্টোরি";
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=payments');
        },
        profile_menu: function () {
            // disable all menus
            this.allMenusDisable();
            this.profile_page = true;
            this.pageTitle = "প্রোফাইল";
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=profile');
        },
        profileTabs: function(tab){
            if(tab === ""){
                this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=profile');
            }else{
                this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=profile&tab='+tab);
            }
        },
        truckImagesSelect: function (event) {
            if (event.target.files && event.target.files[0]) {
                if (event.target.files[0].type.split('/')[0] !== "image") {
                    jQuery(event.target).val("");
                    alert("অবৈধ ছবি!");
                }
            } else {
                jQuery(event.target).val("");
            }
        },
        closeNewTuckForm: function(){
            this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=profile&tab=2');
            location.reload();
        },
        openCancelForm: function (application_id) {
            this.cancelJob.isCancelJobForm = true;
            this.cancelJob.application_id = application_id;
        },
        approveCancelletion: function (application_id, reason) {
            this.cancelJob.application_id = application_id;
            this.cancelJob.cancelReason = reason;
            this.cancelJob.isCancelJobForm = true;
            this.cancelJob.isApprovalForm = true;
        },
        closeCancelForm: function () {
            this.cancelJob.isCancelJobForm = false;
        },
        request_for_cancel: function (event) {
            jQuery.ajax({
                type: "post",
                url: ajaxrequ.ajaxurl,
                data: {
                    action: "cancel_requ_running_job",
                    application_id: trents.cancelJob.application_id,
                    reason: jQuery('#afc_reason').val(),
                    nonce: ajaxrequ.nonce
                },
                beforeSend: () => {
                    jQuery(event.target).find('.cancelLoader').html(`<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                    <path opacity="0.2" fill="#ffffff" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                    s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                    c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"></path>
                    <path fill="#ddd" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                    C22.32,8.481,24.301,9.057,26.013,10.047z">
                    <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.9s" repeatCount="indefinite"></animateTransform>
                    </path></svg>`);
                },
                dataType: "json",
                success: function (response) {
                    jQuery(event.target).find('.cancelLoader').html("");
                    location.reload();
                }
            });
        },
        acceptCancellationRequest: function (event) {
            jQuery.ajax({
                type: "post",
                url: ajaxrequ.ajaxurl,
                data: {
                    action: "accept_cancellation_request",
                    application_id: trents.cancelJob.application_id,
                    nonce: ajaxrequ.nonce
                },
                beforeSend: () => {
                    jQuery(event.target).find('.cancelLoader').html(`<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                    <path opacity="0.2" fill="#ffffff" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                    s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                    c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"></path>
                    <path fill="#ddd" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                    C22.32,8.481,24.301,9.057,26.013,10.047z">
                    <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.9s" repeatCount="indefinite"></animateTransform>
                    </path></svg>`);
                },
                dataType: "json",
                success: function (response) {
                    jQuery(event.target).find('.cancelLoader').html("");
                    location.reload();
                }
            });
        },
        cancel_application_request: function (job_id, application_id, event) {
            let targetEl = jQuery(event.target);

            if (confirm("এপ্লিকেশনটি পুরোপুরি বাতিল হয়ে যাবে")) {
                jQuery.ajax({
                    type: "post",
                    url: ajaxrequ.ajaxurl,
                    data: {
                        action: "cancel_applied_job",
                        job_id: job_id,
                        application_id: application_id,
                        nonce: ajaxrequ.nonce
                    },
                    beforeSend: () => {
                        targetEl.text("বাতিল হচ্ছে...");
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.deleted) {
                            location.reload();
                        } else {
                            targetEl.text("বাতিল করুন");
                        }
                    }
                });
            }
        },
        approve_application: function (job_id, application_id, event) {
            let targetEl = jQuery(event.target);

            if (confirm("আমি এই ড্রাইভারকে ভাড়া নিতে চাই")) {
                jQuery.ajax({
                    type: "post",
                    url: ajaxrequ.ajaxurl,
                    data: {
                        action: "accept_applied_job",
                        job_id: job_id,
                        application_id: application_id,
                        nonce: ajaxrequ.nonce
                    },
                    beforeSend: () => {
                        targetEl.text("প্রক্রিয়াকরণ হচ্ছে...");
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.updated) {
                            location.reload();
                        } else {
                            targetEl.text("গ্রহণ");
                        }
                    }
                });
            }
        },
        current_job_finished: function (job_id, application_id, user='driver', event) {
            let targetEl = jQuery(event.target);
            let alert = 'ক্লাইন্টের কাছে আবেদন পাঠানো হবে';
            if (user === 'client') {
                alert = "গ্রহণ করলে ট্রিপটি শেষ বলে চিহ্নিত হবে";
            }
            if (confirm(alert)) {
                jQuery.ajax({
                    type: "post",
                    url: ajaxrequ.ajaxurl,
                    data: {
                        action: "current_job_finished",
                        job_id: job_id,
                        application_id: application_id,
                        nonce: ajaxrequ.nonce
                    },
                    beforeSend: () => {
                        targetEl.text("প্রক্রিয়াকরণ হচ্ছে...");
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.updated) {
                            location.reload();
                        } else {
                            targetEl.text("সম্পন্ন");
                        }
                    }
                });
            }
        },
        getDocumentType: function () {
            switch (this.driverDocs.documentType) {
                case 'nid-card':
                    this.driverDocs.isDriving = false;
                    this.driverDocs.isPassport = false;
                    this.driverDocs.isNidCard = true;
                    break;
                case 'passport':
                    this.driverDocs.isDriving = false;
                    this.driverDocs.isNidCard = false;
                    this.driverDocs.isPassport = true;
                    break;
                case 'driving_license':
                    this.driverDocs.isNidCard = false;
                    this.driverDocs.isPassport = false;
                    this.driverDocs.isDriving = true;
                    break;
            }  
        },
        previewDocuments: function (event) {
            if (event.target.files && event.target.files[0]) {
                if (event.target.files[0].type.split('/')[0] === "image") {
                    jQuery(event.target).parent().find('.file_view').text(event.target.files[0].name);
                }else {
                    jQuery(event.target).parent().find('.file_view').text("");
                    alert("অবৈধ ছবি!");
                }
            } else {
                jQuery(event.target).parent().find('.file_view').text("");
            }
        },
        machanism: function () {
            // Trip history tab toggle
            jQuery('.trip__title.toggle').on("click", function () {
                jQuery(this).next('.history_table').toggleClass('trnone');
            });
        },
        searchLocations: function () {
            jQuery( "#load_location, #unload_location, #useraddress" ).autocomplete({
                source: function (request, response) {
                    let targets = this;
                    if(request.term.length > 3){
                        jQuery.ajax({
                            type: "get",
                            url: ajaxrequ.ajaxurl,
                            data: {
                                action: "search_locations",
                                address: request.term,
                                nonce: ajaxrequ.nonce
                            },
                            dataType: "json",
                            beforeSend: ()=>{
                                targets.element.parent().addClass('loadbars');
                                targets.element.parent().find('.loading-wrapp').removeClass('trnone');
                            },
                            success: function (data) {
                                targets.element.parent().removeClass('loadbars');
                                targets.element.parent().find('.loading-wrapp').addClass('trnone');
                                response(data.address);
                            }
                        });
                    }
                }
            });
        },
        paymentTripChange: function () { 
            if(trents.paymentTrip > 0){
                jQuery.ajax({
                    type: "get",
                    url: ajaxrequ.ajaxurl,
                    data: {
                        action: "get_trip_due_amount",
                        tripID: trents.paymentTrip,
                        nonce: ajaxrequ.nonce
                    },
                    dataType: "json",
                    beforeSend: function () { 
                        trents.isDisabled = true;
                    },
                    success: function (response) {
                        trents.isDisabled = false;
                        if(response.success){
                            trents.currentDue = response.success;
                        }
                    }
                });
            }else{
                trents.currentDue = 0;
            }
        }
    },
    updated: function () {
        this.machanism();
        this.searchLocations();
    },
    mounted: function () {
        this.machanism();
        this.searchLocations();
    },
    created: function () {
        // Page open
        switch (ajaxrequ.page) {
            case 'progress':
                this.pageTitle = "আবেদন সমূহ";
                this.allMenusDisable();
                this.progress_page = true;
                break;
            case 'active':
                this.pageTitle = "চলমান ট্রিপ্স";
                this.allMenusDisable();
                this.activeJob_page = true;
                break;
            case 'newjob':
                this.pageTitle = "নতুন ট্রিপ যুক্ত";
                this.allMenusDisable();
                this.createJob_page = true;
                break;
            case 'triphistory':
                this.pageTitle = "ট্রিপ হিস্টোরি";
                this.allMenusDisable();
                this.trip_history_page = true;
                break;
            case 'payments':
                this.pageTitle = "পেমেন্ট হিস্টোরি";
                this.allMenusDisable();
                this.payments_page = true;
                break;
            case 'profile':
                this.pageTitle = "প্রোফাইল";
                this.allMenusDisable();
                this.profile_page = true;
                break;
        
            default:
                this.change_url(ajaxrequ.site_url + '/author/' + ajaxrequ.user_login + '?page=progress');
                this.pageTitle = "আবেদন সমূহ";
                this.allMenusDisable();
                this.progress_page = true;
                break;
        }

        this.searchLocations();
        // Open view
        document.getElementById('rents_wrap').style.display = 'block';
    }
});