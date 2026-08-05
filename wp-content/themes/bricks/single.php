<?php
get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$post_id     = get_the_ID();
		$post_type   = get_post_type();
		$bricks_data = Bricks\Helpers::get_bricks_data( $post_id, 'content' );
		$preview_id  = Bricks\Helpers::get_template_setting( 'templatePreviewPostId', $post_id );

		// Render Bricks data
		if ( $bricks_data ) {
			Bricks\Frontend::render_content( $bricks_data );
		}

		// Render default post layout
		elseif ( $post_type === 'post' ) {
			get_template_part( 'template-parts/post' );
		}

		// Previewing Bricks Template without content template assigned: Fallback to preview ID WordPress content
		elseif ( $post_type === BRICKS_DB_TEMPLATE_SLUG && $preview_id ) {
			echo '<main id="brx-content">' . apply_filters( 'the_content', get_post( $preview_id )->post_content ) . '</main>';
		}

		// Default content
		else {
			echo '<main id="brx-content" class="brxe-container layout-default">';

			the_content();

			echo Bricks\Helpers::page_break_navigation();

			echo '</main>';
		}
	}
}
?>
<div id="custom-popup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999;">
    <div class="" style="    height: 100%;
    overflow-y: auto;     padding: 30px;">
  <div style="    background: #fff;
    width: 100%;
    margin: 0px auto;
    padding: 32px;
    position: relative;
    max-width: 768px;
    border-radius: 8px;">
    <span id="close-popup" style="position:absolute; top:10px; right:15px; font-size:40px; cursor:pointer; opacity: 0.7">&times;</span>
    <h3 style="    font-size: 36px;
    color: #022b69;">Instant Booking</h3>
    <!--Instant Booking form1 start-->
    <div id="instant-booking-form1" class="brxe-block" style="margin-top: 24px;">
                            <div class="form-heading brxe-block">
                                <h4 class="brxe-heading heading-style-h4 font-weight-normal">
                                    Enter your information
                                </h4>
                            </div>
                            <form method="post" class="estimate-form-inner-wrapper" id="form-step-1">
                                <div class="estimate-form-input-grid">
                                    <div class="estimate-form-input-wrapper">
                                        <p>Name <span>*</span></p>
                                        <input type="text" id="fNameInfo" name="Name" required=""
                                            placeholder="First Name" />
                                    </div>
                                    <div class="estimate-form-input-wrapper">
                                        <input type="text" name="lName" id="lNameInfo" placeholder="Last Name" />
                                    </div>
                                </div>
                                <div class="estimate-form-input-grid">
                                    <div class="estimate-form-input-wrapper">
                                        <p>Email <span>*</span></p>
                                        <input type="email" id="emailInfo" name="Email" required=""
                                            placeholder="Enter Email" />
                                    </div>
                                    <div class="estimate-form-input-wrapper">
                                        <p>Phone Number <span>*</span></p>
                                        <input type="tel" id="phoneInfo" name="Phone1" maxlength="14"
                                            placeholder="Enter phone number" required="" />
                                    </div>
                                </div>
                                <div class="estimate-form-input-grid">
                                    <div class="estimate-form-input-wrapper">
                                        <p>Address line 1<span>*</span></p>
                                        <input type="text" id="address1Info" name="Address1" required="" />
                                    </div>
                                    <div class="estimate-form-input-wrapper">
                                        <p>Address line 2</p>
                                        <input type="text" id="address2Info" name="Address2" />
                                    </div>
                                </div>
                                <div class="estimate-form-input-grid">
                                    <div class="estimate-form-input-wrapper">
                                        <p>City<span>*</span></p>
                                        <input type="text" aria-required="true" aria-invalid="false" id="cityInfo"
                                            name="City"  required=""/>
                                    </div>
                                    <div class="estimate-form-input-wrapper">
                                        <p>State<span>*</span></p>
                                        <input type="text" aria-required="true" aria-invalid="false" id="stateInfo"
                                            name="State" required="" />
                                    </div>
                                </div>
                                <div class="estimate-form-input-grid">
                                    <div class="estimate-form-input-wrapper">
                                        <p>Zip Code <span>*</span></p>
                                        <input type="text" id="postalcodeInfo" name="PostalCode" required="" />
                                    </div>
                                </div>
                                <button type="submit" class="estimate-submit-btn">
                                    Next
                                </button>
                            </form>
                            <div id="errorMessageFormStep1" style="display:none; color: red; margin-top: 16px">
                                There was an error submitting the form. Please try again.
                            </div>
    </div>
    <!--Instant Booking form1 end-->
    <!--Instant Booking form2 start-->
    <div id="instant-booking-form2" class="brxe-block" style="margin-top: 24px;">
                            <div class="form-heading brxe-block" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; flex-direction: row;">
                                <h4 class="brxe-heading heading-style-h4 font-weight-normal">
                                    2. Select a Slot
                                </h4>
                                <button class="estimate-submit-btn" id="back-to-step1">
                                    Back
                                </button>
                            </div>
                            <form method="post" class="estimate-form-inner-wrapper" style="width: 100%;
    margin-bottom: 16px !important;
    margin-top: 24px;">
                                <div class="estimate-form-input-wrapper">
                                    <p>Select a date</p>
                                    <input type="text" id="datepicker" />
                                </div>
                            </form>
                            <div class="success-container brxe-block" id="no-slots-container" style="    margin-bottom: 24px;">
                                <div class="success-wrapper brxe-block" style="    align-items: center;font-size: 18px;font-style: italic;margin-top: 16px;text-align: center;opacity: 0.8;">
                                    <p>There are no slots available for the selected date.</p>
                                </div>
                            </div>
                            <div class="slots-container" id="slots-container" style="flex-wrap:wrap;     margin-bottom: 24px;     width: 100%;">
                                
                            </div>
                            <div class="step-2-submit-btn-wrapper brxe-block" id="booking-submit-btn-wrapper">
                                <button class="estimate-submit-btn" id="booking-submit-btn">
                                    Next
                                </button>
                            </div>
                            <div style="display: none;" id="slot-data-container">
                                <div id="DateTime">DateTime</div>
                                <div id="DateTimeFormatted">DateTimeFormatted</div>
                                <div id="DriveTimeMinutes">DriveTimeMinutes</div>
                                <div id="ServiceAgentId">ServiceAgentId</div>
                                <div id="ServiceAgentName">ServiceAgentName</div>
                            </div>
    </div>
    <!--Instant Booking form2 end-->
    <!--Instant Booking form3 start-->
    <div id="instant-booking-form3" class="brxe-block">
	    <div class="success-container brxe-block" style="    margin-top: 36px;">
	        <div class="brxe-block" id="success-container" style="align-items: center; font-size: 24px;"></div>
	        <a id="intant-booking-back-to-home-btn" href="<?php echo $location_url ?? '' ?>" style="    margin-top: 56px;
    font-size: 20px;
    text-decoration: underline;
    color: #8ac042;">
	            Back to Home
	        </a>
	    </div>
    </div>
    <!--Instant Booking form3 end-->
    <div id="loader" class="brxe-block">
        <!-- <dotlottie-player
        src="https://lottie.host/96a7b196-00ae-4579-bb46-3582adefc14f/eXATJq77vp.lottie"
        background="transparent" speed="1" style="width: 128px; height: 128px" loop
        autoplay></dotlottie-player> -->
        <div id="custom_loader_circle"></div>
    </div>
  </div>
</div>
</div>


<style type="text/css">
    #custom_loader_circle {
  border: 8px solid #f3f3f3;
  border-radius: 50%;
  border-top: 8px solid #043968;
  width: 48px;
  height: 48px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
      position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var openBtn = document.getElementById("open-popup");
    var closeBtn = document.getElementById("close-popup");
    var popup = document.getElementById("custom-popup");

    openBtn.addEventListener("click", function (e) {
        e.preventDefault();
        popup.style.display = "block";
    });

    closeBtn.addEventListener("click", function () {
        popup.style.display = "none";
    });

    window.addEventListener("click", function (e) {
        if (e.target === popup) {
            popup.style.display = "none";
        }
    });

    const createCustomerForm = document.getElementById("form-step-1");
    const backToStep1Btn = document.getElementById("back-to-step1");
    const noSlotsContainer = document.getElementById("no-slots-container");
    const bookingSubmitBtnWrapper = document.getElementById("booking-submit-btn-wrapper");

    let apiKeyFromSlots;
    let contactIdFromSlots;
    let serviceIdFromSlots;

    /**Working on back step-1 button start**/
    backToStep1Btn.addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("instant-booking-form2").style.display = "none";
        document.getElementById("instant-booking-form1").style.display = "flex";

        $(function () {
            $("#datepicker").datepicker({
                minDate: 0,
            }).datepicker("setDate", "today");  // Set the datepicker to today
        });
    });
    /**Working on back step1 button end**/

        /**code for slot click function start**/
          function handleSlotClick()
          {
                
                document.querySelectorAll('.slots-wrapper').forEach(slotElement => {
                    slotElement.addEventListener('click', function () {
                        // Remove 'slot-active' from all slots
                        document.querySelectorAll('.slots-wrapper').forEach(el => el.classList.remove('slot-active'));

                        // Add 'slot-active' to the clicked slot
                        this.classList.add('slot-active');

                        // Get the id of the clicked slot (e.g., 'slot-0')
                        const clickedSlotId = this.id;
                       // console.log('Clicked slot ID:', clickedSlotId);

                        // Check if 'slots' data exists in localStorage
                        const storedSlots = JSON.parse(localStorage.getItem('slots'));

                        // Check if storedSlots is not null and contains data
                        if (storedSlots && Array.isArray(storedSlots)) {
                            // Find the slot data that matches the clicked slot's ID
                            const clickedSlotData = storedSlots.find(slot => slot.id === clickedSlotId);
                          //  console.log('Clicked slot data:', clickedSlotData);
                            document.getElementById("DateTime").textContent = clickedSlotData.DateTime;
                            document.getElementById("DateTimeFormatted").textContent = clickedSlotData.DateTimeFormatted;
                            document.getElementById("DriveTimeMinutes").textContent = clickedSlotData.DriveTimeMinutes;
                            document.getElementById("ServiceAgentId").textContent = clickedSlotData.ServiceAgentId;
                            document.getElementById("ServiceAgentName").textContent = clickedSlotData.ServiceAgentName;
                        }
                    });
                });
          }
        /**code for slot click function end**/

        /**code for slot generation start**/
        function handleSlotsGenerations(date, apiKey, contactId, serviceId) {
                let data = {
                    action: "get_slots_from_date",
                    nonce: "<?php echo wp_create_nonce('get_slots_from_date_nonce'); ?>",
                    contact_id: contactId,
                    service_id: serviceId,
                    selected_date: date,
                    api_key: apiKey,
                };

                fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams(data).toString(),
                })
                    .then(response => response.json())
                    .then(result => {
                        const slotsContainer = document.getElementById("slots-container"); // Make sure you have a container element
                        slotsContainer.innerHTML = "";

                        if (result?.data?.response?.Slots.length > 0) {
                            slotsContainer.style.display = "flex";
                            bookingSubmitBtnWrapper.style.display = "flex";
                            noSlotsContainer.style.display = "none";

                            let slotsArray = [];

                            result.data.response.Slots.forEach((slot, index) => {
                                // Create slot element
                                const slotElement = document.createElement("div");
                                slotElement.className = "slots-wrapper";
                                slotElement.id = `slot-${index}`;
                                slotElement.innerHTML = `<p>${slot.DateTimeFormatted}<br />with <br />${slot.ServiceAgentName}</p>`;
                                // Append to container
                                slotsContainer.appendChild(slotElement);

                                slotsArray.push({
                                    id: `slot-${index}`,
                                    DateTime: slot.DateTime,
                                    DateTimeFormatted: slot.DateTimeFormatted,
                                    ServiceAgentId: slot.ServiceAgentId,
                                    ServiceAgentName: slot.ServiceAgentName,
                                    DriveTimeMinutes: slot.DriveTimeMinutes
                                });
                            });

                            handleSlotClick();

                            localStorage.setItem('slots', JSON.stringify(slotsArray));
                        } else {
                            slotsContainer.style.display = "none";
                            bookingSubmitBtnWrapper.style.display = "none";
                            noSlotsContainer.style.display = "flex";
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred while submitting the data.");
                    });
            }
        /**code for slot generation end**/
        /**code for slot booking start**/
        function handleBooking(apiKey, contactId, serviceId) {
                let data = {
                    action: "create_a_booking",
                    nonce: "<?php echo wp_create_nonce('create_a_booking_nonce'); ?>",
                    contact_id: contactId,
                    service_id: serviceId,
                    data_time: document.getElementById("DateTime").textContent,
                    data_time_formatted: document.getElementById("DateTimeFormatted").textContent,
                    service_agent_id: parseInt(document.getElementById("ServiceAgentId").textContent, 10),
                    service_agent_name: document.getElementById("ServiceAgentName").textContent,
                    drive_time_minutes: parseInt(document.getElementById("DriveTimeMinutes").textContent, 10),
                    api_key: apiKey,
                };

                fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams(data).toString(),
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result?.data?.response?.ResultCode !== 1 || result?.success !== false) {
                            document.getElementById("loader").style.display = "none";
                            document.getElementById("instant-booking-form2").style.display = "none";
                            document.getElementById("instant-booking-form3").style.display = "flex";

                            const successContainer = document.getElementById("success-container");
                            const successElement = document.createElement("div");
                            successElement.classList.add("success-wrapper", "brxe-block");
                            successElement.innerHTML = `<p>Your appointment is confirmed for</p><b>${result?.data?.response?.Slots[0]?.DateTimeFormatted} with ${result?.data?.response?.Slots[0]?.ServiceAgentName}</b>`;
                            // Append to container
                            successContainer.appendChild(successElement);
                        } else {
                            document.getElementById("loader").style.display = "none";
                            alert("Booking was unsuccessful!");
                        }
                    })
                    .catch(error => {
                        document.getElementById("loader").style.display = "none";
                        console.error("Error:", error);
                        alert("An error occurred while submitting the data.");
                    });
        }
        /**code for slot booking start**/


        // Ensure the form is bound only once
        if (!createCustomerForm.dataset.bound) {
                    createCustomerForm.dataset.bound = "true";

                    // Attach submit event listener
                    createCustomerForm.addEventListener("submit", function (event) {
                        event.preventDefault();
                        console.log("Submit click");

                        const zip = document.getElementById("postalcodeInfo").value.trim();
                        const locations = document.querySelectorAll(".single-location");
                        
                        // Reusable function to submit form via AJAX
                        function submitForm(key, serviceId, zipValue) {
                        	console.log("service Id "+serviceId);
                            const firstName = document.getElementById("fNameInfo").value;
                            const lastName = document.getElementById("lNameInfo").value;
                            const email = document.getElementById("emailInfo").value;
                            const phone = document.getElementById("phoneInfo").value;
                            const address1 = document.getElementById("address1Info").value;
                            const address2 = document.getElementById("address2Info").value;
                            const city = document.getElementById("cityInfo").value;
                            const state = document.getElementById("stateInfo").value;


                            const data = {
                                first_name: firstName,
                                last_name: lastName,
                                email: email,
                                mobile_number: phone,
                                address1: address1,
                                address2: address2,
                                city: city,
                                state: state,
                                zip: zipValue,
                                service_id: parseInt(serviceId, 10),
                                key: key,
                                nonce:
                                    "<?php echo wp_create_nonce('create_customer_form_nonce'); ?>",
                                action: "submit_estimate_sm_form",
                            };

                            document.getElementById("errorMessageFormStep1").style.display =
                                "none";

                            document.getElementById("loader").style.display = "flex";
                            // Send AJAX request to WordPress backend
                            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded",
                                },
                                body: new URLSearchParams(data).toString(),
                            })
                                .then((response) => response.json())
                                .then((result) => {
                                 console.log("result code slot");
                                 console.log(result.data.slots_response.ResultCode);
                                    if (result?.data?.slots_response?.ResultCode === 0) {
                                        document.getElementById("instant-booking-form1").style.display = "none";
                                        document.getElementById("instant-booking-form2").style.display = "flex";
                                        document.getElementById("loader").style.display = "none";

                                        apiKeyFromSlots = result?.data?.slots_response?.ApiKey;
                                        contactIdFromSlots = result?.data?.slots_response?.ContactId;
                                        serviceIdFromSlots = result?.data?.slots_response?.ServiceId;

                                        const slotsContainer = document.getElementById("slots-container"); // Make sure you have a container element
                                        slotsContainer.innerHTML = "";

                                        if (result?.data?.slots_response?.Slots.length > 0) {
                                            slotsContainer.style.display = "flex";
                                            bookingSubmitBtnWrapper.style.display = "flex";
                                            noSlotsContainer.style.display = "none";

                                            let slotsArray = [];

                                            result.data.slots_response.Slots.forEach((slot, index) => {
                                                // Create slot element
                                                const slotElement = document.createElement("div");
                                                slotElement.className = "slots-wrapper";
                                                slotElement.id = `slot-${index}`;
                                                slotElement.innerHTML = `<p>${slot.DateTimeFormatted}<br />with <br />${slot.ServiceAgentName}</p>`;
                                                // Append to container
                                                slotsContainer.appendChild(slotElement);

                                                slotsArray.push({
                                                    id: `slot-${index}`,
                                                    DateTime: slot.DateTime,
                                                    DateTimeFormatted: slot.DateTimeFormatted,
                                                    ServiceAgentId: slot.ServiceAgentId,
                                                    ServiceAgentName: slot.ServiceAgentName,
                                                    DriveTimeMinutes: slot.DriveTimeMinutes
                                                });
                                            });

                                            handleSlotClick();

                                            localStorage.setItem('slots', JSON.stringify(slotsArray));
                                        } else {
                                            slotsContainer.style.display = "none";
                                            bookingSubmitBtnWrapper.style.display = "none";
                                            noSlotsContainer.style.display = "flex";
                                        }
                                    } else {

                                        document.getElementById("loader").style.display = "none";
                                        showError("Submission failed. Please try again.");
                                    }
                                })
                                .catch((error) => {
                                    document.getElementById("loader").style.display = "none";
                                    console.error("AJAX Error:", error);
                                    showError("An error occurred. Please try again.");

                                });
                        }

                        // Function to display error message
                        function showError(message) {
                            const errorMessage = document.getElementById("errorMessageFormStep1");
                            errorMessage.textContent = message;
                            errorMessage.style.display = "block";

                            document.getElementById("loader").style.display = "none";

                            setTimeout(() => {
                                errorMessage.style.display = "none";
                            }, 3000);
                        }

                        // Check ZIP code match in locations
                        let matchFound = false;

                        locations.forEach(function (location) {
                            const zipcode = location.querySelector(".zipcode").textContent.trim();
                            const additionalZipcodes = location
                                .querySelector(".additional-zipcodes")
                                .textContent.trim()
                                .split(/\s*,\s*/);

                                
                            if (zipcode === zip || additionalZipcodes.includes(zip)) {

                            	
                                const locationSmKey = location
                                    .querySelector(".location-service-minder-key")
                                    .textContent.trim();
                                const locationSmServiceId = location
                                    .querySelector(".service-minder-id")
                                    .textContent.trim();

                                    //locationSmServiceId=148666;
                                   
                                if (locationSmKey && locationSmServiceId) {
                                    console.log("submitted");
                                    submitForm(locationSmKey,locationSmServiceId,zip);
                                    matchFound = true; // Mark as found
                                    return;
                                } else {
                                    console.warn("Try entering another zip code change.");
                                    matchFound = true; // Mark as found even if there's no key
                                }
                            }
                        });

                        // If no match was found, show an alert
                        if (!matchFound) {
                            alert("Try entering another zip code.");
                        }

                    });

                    //Phone input formatting
                    document
                        .getElementById("phoneInfo")
                        .addEventListener("input", function (e) {
                            const x = e.target.value
                                .replace(/\D/g, "")
                                .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                            e.target.value = !x[2]
                                ? x[1]
                                : `(${x[1]}) ${x[2]}${x[3] ? `-${x[3]}` : ""}`;
                        });
         }

     document.getElementById("booking-submit-btn").addEventListener("click", function () {
                document.getElementById("loader").style.display = "flex";
                handleBooking(apiKeyFromSlots, contactIdFromSlots, serviceIdFromSlots);
     });

    document.getElementById("intant-booking-back-to-home-btn").addEventListener("click", function () {
        createCustomerForm.reset();
    });

     $(function(){
        
        $("#datepicker").datepicker({
            onSelect: function (date) {
                handleSlotsGenerations(date, apiKeyFromSlots, contactIdFromSlots, serviceIdFromSlots)
            },
            minDate: 0,
        }).datepicker("setDate", "today");

        
        // /**code for address places start**/
        // var input = document.getElementById('address1Info');
        // var autocomplete = new google.maps.places.Autocomplete(input);

        // // Set the fields to return for the PlaceResult object
        // autocomplete.setFields(['address_components', 'geometry']);

        // // Handle the PlaceResult object when a user selects an address
        // autocomplete.addListener('place_changed', function() {
        //     console.log("place changes");
        //     var place = autocomplete.getPlace();
        //     // Get the address components from the PlaceResult object
        //     var address_components = place.address_components;
        //     // Loop through the components to find the state, city, and ZIP code
        //     var state = "";
        //     var city = "";
        //     var zip = "";
        //     var route = "";
        //     var street_number = "";
        //     for (var i = 0; i < address_components.length; i++) {
        //         var component = address_components[i];

                
        //         if (component.types.includes('administrative_area_level_1')) {
        //             state = component.short_name;
        //         } else if (component.types.includes('locality')) {
        //             city = component.long_name;
        //         } else if (component.types.includes('postal_code')) {
        //             zip = component.short_name;
        //         }else if (component.types.includes('street_number')) {
        //             street_number = component.long_name;
        //         }else if (component.types.includes('route')) {
        //             route = component.long_name;
        //         }
        //     }
        //     if (street_number && route) {
        //         $('#address1Info').val(street_number+" "+route);
        //     }
        //     // Auto-fill the state, city, and ZIP code fields
        //     $('#stateInfo').val(state);
        //     $('#cityInfo').val(city);
        //     $('#postalcodeInfo').val(zip);
        // });
        //         /**code for address places end**/
     });
});
</script>
<style type="text/css">

#instant-booking-form2, #instant-booking-form3,#loader{
    display:none;
}
.slots-wrapper {
    margin:4px;
    padding: 8px;
    width: 15%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
    border-radius: 10px;
    background-color:#8ac042;
    color: white;
    transition: all 0.3s ease;
    cursor: pointer;
}

.slot-active {
    background-color: #60931b;
} 
#form-step-1{ 
    width: 100%;
    margin-top: 16px;
}
.slots-wrapper { 
    width: 32%; 
}
.slots-wrapper p {
    font-size: 18px;
    line-height: 1.4;
}
.slots-wrapper:hover {
    background: #022b69;
}
#loader{
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background: rgb(255 255 255 / 56%);
    align-items: center;
    justify-content: center;
}
</style>

<?php
get_footer();
