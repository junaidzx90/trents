jQuery(function ($) {
    $(document).on("click", '.openjob', function () {
        let jobdetailsBox = $('.popupcontents');
        let jobId = $(this).parents('.job').find('.job_ID').val();

        $('#application-popup').removeClass('trnone');
        $('#application-popup .popup-inside').animate({
            width: "65%"
        });

        $(document).on("click", function (e) {
            if ($(e.target).hasClass('popup-wrap') || $(e.target).hasClass('pop-closeicon')) {
                $('#application-popup').addClass('trnone');
                $('#application-popup .popup-inside').animate({
                    width: "0%"
                });
                jobdetailsBox.html("");
            }
        });

        let openJobLoader = `<div class="tr-loader-icon">
            <svg version="1.1" id="tr-loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="80px" height="80px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z" />
                <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                C22.32,8.481,24.301,9.057,26.013,10.047z">
                <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.9s" repeatCount="indefinite" />
                </path>
            </svg>
        </div>`;
        
        if (jobId > 0) {
            $.ajax({
                type: "get",
                url: jobajax.ajaxurl,
                data: {
                    action: "get_job_details_for_apply",
                    nonce: jobajax.nonce,
                    job_id: jobId
                },
                dataType: "json",
                beforeSend: () => {
                    jobdetailsBox.html(openJobLoader);
                },
                success: function (response) {
                    if (response.success) {
                        let applyform = `<div class="applyform">
                            <form action="" method="post">
                                <h3 class="titleof_job">${response.success}</h3>
                                <div class="cost__hint">
                                    <p>সর্বমোট <span class="total__hint">0</span></p>
                                    <p class="cost__guid">${jobajax.vats}% ভ্যাট প্রযোজ্য</p>
                                </div>
                                <div class="formdata">
                                    <input type="hidden" value="${jobId}" name="job_id"/>
                                    <div class="forminp">
                                        <label for="__rent_cost">ভাড়া</label>
                                        <input type="number" placeholder="১০০০ টাকা" id="__rent_cost" name="rent_cost"/>
                                    </div>
                                    <div class="forminp">
                                        <label for="__application">আবেদনপত্র</label>
                                        <textarea id="__application" name="application" rows="5"></textarea>
                                    </div>

                                    <input type="submit" class="application__form_btn" name="application__form_btn" value="জমা দিন" />
                                </div>
                            </form>
                        </div>`;
                        jobdetailsBox.html(applyform);
                    }
                    if (response.unverified) {
                        let applyform = `<div class="applyform">
                            <div class="unverifiedNotice"><h1 class="unverifiedTitle">আপনার একাউন্টটি ভেরিফাই নয়!</h1></div>
                        </div>`;
                        jobdetailsBox.html(applyform);
                    }
                    if (response.needtologin) {
                        let applyform = `<div class="applyform">
                            <div class="loginbox"><a href="${response.needtologin}">ড্রাইভার একাউন্টে লগইন করুন</a></div>
                        </div>`;
                        jobdetailsBox.html(applyform);
                    }
                }
            });
        }

        $(document).on("keyup", "#__rent_cost", function () {
            let vats = jobajax.vats;
            vats = parseInt(vats);
            let total = $(this).val();
            total = parseInt(total);

            let hints = $(".total__hint");
            if (total > 0) {
                hints.text(total + (vats / 100 * total));
            } else {
                hints.text(0);
            }
        })
    });
});