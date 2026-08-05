<?php
/* Template Name: Privacy Policy */
get_header();

// Get the current URL path
$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Split the URL into segments
$segments = explode('/', $current_url);

// Determine if the URL is location-specific or generic
if (count($segments) === 2 && $segments[1] === 'privacy-policy') {
  $location_slug = $segments[0];
  show_location_page($location_slug);

} elseif (count($segments) === 1 && $segments[0] === 'privacy-policy') {
  render_fallback_content();

} else {
  // Invalid or unexpected URL structure
  echo "Page not found.";
  get_template_part('404');
  exit;
}

get_footer();

function show_location_page($location_slug)
{
  // Get the location post by slug
  $location_post = get_page_by_path($location_slug, OBJECT, 'location');

  if (!$location_post) {
    echo "Location not found.";
    return;
  }

  // Get the location ID
  $location_id = $location_post->ID;

  $location_title = get_the_title($location_id);
  ?>
  <main id="brx-content">
    <section id="brxe-kikkzc" class="brxe-section section">
      <div id="brxe-wtsixe" class="brxe-container padding-global">
        <div id="brxe-isdpcy" class="brxe-block">
          <div id="brxe-hlomay" class="brxe-block section-component">
            <div id="brxe-osvhli" class="brxe-block">
              <div id="brxe-rvzcqy" class="brxe-block">
                <h2 id="brxe-qbsfxd" class="brxe-heading heading-style-h2 is-blue" data-animi="up" data-duration="0.6"
                  data-delay="0.2">
                  Privacy Policy of <?php echo $location_title ?>
                </h2>
              </div>
              <div id="brxe-dllemy" class="brxe-text rich-text" data-animi="up" data-delay="0.3" data-duration="0.6">
                <div class="terms_grid">
                  <h3>1. Categories of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Koala Insulation Franchisor, LLC is the owner and operator of this
                    website as well as the owner of Koala Insulation trademarks
                    and associated identifiers. Koala Insulation Franchisor, LLC does not
                    operate any insulation businesses. All insulation services are
                    provided by independently owned and operated licensees or
                    franchisees of the Koala Insulation brand under agreements
                    governing such licenses and/or franchises. All information
                    collected will be retained by Koala Insulation Franchisor, LLC
					and passed on to the appropriate franchisee or licensee or related
                    company (collectively, “Affiliate”). Information provided to
                    Affiliates may also be provided to Koala Insulation Franchisor,
					LLC and will be retained in accordance with this policy and
					applicable law.
                  </p>
                  <p>
                    (b) Information You Provide to Us. When you place an order,
                    open an account, request a quote, participate in a
                    questionnaire, communicate with customer service or send us an
                    email, you provide us with information that we collect. Such
                    information may include your name, address, phone number,
                    credit card information, home or building information and
                    purchase history.
                  </p>
                  <p>
                    (c) Other Information We Receive and Store. We also receive
                    information from you as a result of your visiting our site,
                    interacting with our webpage or its links, sending us emails
                    including your IP address, your home or building data, your
                    email address, your browser type and version, your operating
                    system and platform, your purchase history, products you view,
                    products you search for, length of visits to pages and other
                    browser interaction information.
                  </p>
                  <p>
                    (d) All information provided by you to Koala Insulation or any
                    Affiliate is the property of the receiving party and/or Koala
                    Insulation as governed by agreements between the Affiliate and
                    Koala Insulation. Once you voluntarily provide information to
                    us, we may retain it indefinitely and use it in the course of
                    our business.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>2. Use and Disclosure of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Use and Disclosure of Information. We will use and
                    disclose your information as follows: (1) to permit you to
                    review information online; (2) to analyze your needs and
                    eligibility for certain products or services that we or our
                    affiliates may offer; (3) to solicit you or subsequent owners
                    of your building or home for these services; (4) to ensure a
                    high quality customer experience; (5) to assist you with your
                    requested services; (6) to confirm and ship your orders or
                    schedule your estimate or install; (7) to follow up to make
                    sure such orders are fulfilled; (8) if, in the course of
                    placing an order and/or requesting information, you opt to
                    receive occasional emails from us notifying you of special
                    offers, we will use such information to notify you of such
                    offers; (9) if, in the course of placing an order and/or
                    entering a contest, you opt to receive occasional emails from
                    third parties to whom we provide your information, we will
                    disclose such information to such third parties; (10) to
                    protect the rights and safety of us, our shareholders,
                    members, officers, employees and customers; (11) at our
                    option, to notify you of any changes to this Privacy Policy or
                    our other terms and conditions of this website (you are bound
                    by the terms of any changes we post to our website whether or
                    not we notify you by email) and (12) as otherwise required by
                    law. We disclose your information to third parties only as
                    reasonably required to fulfill your orders and collect sums
                    due us (such as to shippers and credit card processors), to
                    protect the rights and safety of us, our shareholders,
                    members, employees and customers (such as to legal
                    representatives and law enforcement), as you expressly permit,
                    and as required by law (such as the result of a court
                    subpoena). We may transfer your information in the event of
                    the sale of substantially all of the assets of our business to
                    a third-party or in the event of a merger, consolidation or
                    acquisition. However, in such event, any acquirer will be
                    subject to the provisions of our commitments to you.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>3. Do Not Track.</h3>
                  <p></p>
                  <p>
                    We do not monitor, recognize, or respond to any opt-out or do
                    not track mechanisms, including general web browser “Do Not
                    Track” settings and/or signals. Further, we do not authorize
                    third parties to collect any personally identifiable
                    information about individuals who visit our site without
                    separate consent.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>4. Notification of Changes.</h3>
                  <p></p>
                  <p>
                    (a) Notification of Changes. Any changes in our Privacy Policy
                    will be posted to this website and will become effective as of
                    the date of posting with respect to information we then
                    collect in the future, but will not be changed with respect to
                    information that we have then already collected. It is and
                    will be your responsibility to review our Privacy Policy from
                    time to time to make sure you are aware of any changes.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>5. Effective Date.</h3>
                  <p></p>
                  <p>
                    (a) Effective Date. This Privacy Policy is effective with
                    respect to all data that we have collected since the date we
                    started collecting data, which was January 1, 2019.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>6. Questions.</h3>
                  <p></p>
                  <p>
                    (a) Questions. If you have any questions about our Privacy
                    Policy, feel free to contact us by sending a letter to: 
					Koala Insulation ATTN: Privacy Manager, 445 West Drive,
                    Melbourne, FL 32904.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>7. Limitation of Liability and Choice of Venue.</h3>
                  <p></p>
                  <p>
                    Under no circumstances shall the liability of any user
                    associated with this website exceed the amount the user has
                    paid to the website owner. Any dispute arising out of or
                    relating to this policy or any use or viewing of this website
                    shall be brought through arbitration in Brevard County,
                    Florida. In the event that this agreement to arbitrate is not
                    upheld, any court proceedings shall be in Brevard County
                    Florida and each user irrevocably consents and waives any
                    objections including without limitation objections to this
                    venue for forum non conveniens, in the courts having
                    jurisdiction over Brevard County Florida.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>8. Information Use</h3>
                  <p></p>
                  <p>
					We use the collected information primarily for our own internal 
				    purposes, such as providing, maintaining, evaluating, and 
				    improving our services and Website, fulfilling requests for 
					information, and providing customer support.
                  </p>
                  <p>
					To Communicate with You: We use your contact information to 
					respond to your inquiries, provide legal services, send 
					administrative information, and keep you informed about 
					your case or our services.
                  </p>
                  <p>
					Marketing and Promotional Communications: With your consent, 
					we may use your information to send you updates, newsletters, 
					or marketing communications via email, phone, or text message. 
					You can opt out of receiving these communications at any time 
					by following the instructions provided in the communication or
					contacting us directly. 
                  </p>
                  <p>
					Legal Compliance: We may use your information to comply with
					applicable laws, regulations, or legal obligations, including
					responding to subpoenas, court orders, or legal requests.  
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>9. Security</h3>
                  <p></p>
                  <p>
					We follow generally accepted industry standards to protect the
					information submitted to us, both during transmission and once
					we receive it.
                  </p>
                  <p>
					If we collect sensitive information (such as credit card data),
					that information is encrypted and transmitted to us in a secure way.
					You can verify this by looking for a closed lock icon at the bottom
					of your web browser, or looking for “https” at the beginning of 
					the address of the web page.
                  </p>
                  <p>
					While we use encryption to protect sensitive information transmitted
					online, we also protect your information offline. Only employees
					who need the information to perform a specific job (for example,
					billing or customer service) are granted access to personally
					identifiable information. The computers/servers in which we store
					personally identifiable information are kept in a secure environment.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>10. Cookies</h3>
                  <p></p>
                  <p>
					We use “cookies” on this site. A cookie is a piece of data stored
					on a site visitor’s hard drive to help us improve your access to
					our site and identify repeat visitors to our site. For instance,
					when we use a cookie to identify you, you would not have to log
					in a password more than once, thereby saving time while on our site.
					Cookies can also enable us to track and target the interests of our
					users to enhance the experience on our site. Usage of a cookie is
					in no way linked to any personally identifiable information on our
					site. 
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>11. Sharing</h3>
                  <p></p>
                  <p>
					We do not sell or rent your personal information to third parties.
					We do not sell, rent, release, or transfer your SMS consent or
					phone number to any third party for any third party marketing
					purposes. We may share your information in the following
					circumstances:
                  </p>
                  <p>
					Service Providers: We may share your information with our 
					service providers who perform services on our behalf, 
					such as marketing, customer services, or technical support.
					These service providers are contractually obligated to 
					protect your information and use it only for services they
					provide.
                  </p>
                  <p>
					Legal Requirements: We may disclose your information if
					required by law, regulation, or legal process, or if we
					believe disclosure is necessary to protect our rights,
					property, or the safety of our users or others.
                  </p>
                  <p>
					We will disclose personal information and/or an IP address,
					when required by law or in the good-faith belief that such
					action is necessary to:
                  </p>
                  <p>
					Cooperate with the investigations of purported unlawful
					activities and conform to the edicts of the law or comply
					with legal process served on our company
                  </p>
                  <p>
					Protect and defend the rights or property of our Website
					and related properties
                  </p>
                  <p>
					Identify persons who may be violating the law, the rights
					of third parties, or otherwise misusing our Website or its
					related properties
                  </p>
                  <p>
					Please keep in mind that whenever you voluntarily disclose
					personal information online – for example through e-mail,
					discussion boards, or elsewhere – that information can be
					collected and used by others. In short, if you post personal
					information online that is accessible to the public, you may
					receive unsolicited messages from other parties in return.
                  </p>
                  <p>
					Ultimately, you are solely responsible for maintaining the
					secrecy of your personal information. Please be careful and
					responsible whenever you are online.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>12. Consent</h3>
                  <p></p>
                  <p>
					By using this Website, you consent to the collection and use
					of information as specified above. If we make changes to our
					Privacy Policy, we will post those changes on this page.
					Please review this page frequently to remain up-to-date with
					the information we collect, how we use it, and under what
					circumstances we disclose it. You must review the new Privacy
					Policy carefully to make sure you understand our practices 
					and procedures.
                  </p>
                  <p>
					You are not required to consent to receiving text messages
					from Koala Insulation. By providing your phone number and
					opting in, you consent to receive text messages from Koala
					Insulation regarding your inquiry, our services, or related
					legal matters. Message and data rates may apply. You can opt
					out of receiving text messages at any time by replying “STOP”
					to any text message you receive from us. Please note that
					opting out may limit our ability to communicate with you
					regarding your case or services.
                  </p>
                  <p>
					Opting Out: You may opt out of receiving marketing communications
					from us by following the instructions in those communications or
					contacting us directly. If you opt out, we may still send you 
					non-promotional communications related to your legal services
					or our ongoing business relationship.
                  </p>
                  <p>
					Access and Update Information: You have the right to access,
					update, or correct your personal information. To do so, please
					contact us using the information provided below. 
                  </p>
                  <p>
                    Message Frequency will vary from 1-10 messages in a week, pending your inquiry and service needs.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>13. Email and Data Collection</h3>
                  <p></p>
                  <p>
					By using our website and services, you consent to the collection,
					processing, and storage of SMS and email data as described in this
					privacy policy. We may collect SMS and email data for various
					purposes, including but not limited to:
                  </p>
                  <p>
					Communication: We may use SMS and email to communicate with users
					regarding account-related information, updates, announcements, and
					other relevant notifications.
                  </p>
                  <p>
					Service Delivery: SMS and email data may be collected to facilitate
					the delivery of our services, including account verification,
					password resets, transactional messages, and customer support.
                  </p>
                  <p>
					Marketing: With your explicit consent, we may use SMS and email
					to send promotional offers, newsletters, marketing campaigns,
					and other communications about our products or services. 
					You have the option to opt-out of marketing communications at 
					any time.
                  </p>
                  <p>
					Analytics: We may collect SMS and email data for analytical purposes
					to understand user behavior, improve our services, and personalize
					user experiences.
                  </p>
                  <p>
					We are committed to protecting the privacy and security of your SMS
					and email data. We do not share SMS and email data outside of our
					organization, except where required by law or as necessary to 
					provide the services requested by the user. Your SMS and email data
					is treated confidentially and securely, and we employ
					industry-standard measures to prevent unauthorized access, misuse,
					or alteration.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>14. SMS Data Handling</h3>
                  <p></p>
                  <p>
					We understand the importance of safeguarding the privacy of
					our users, including the data transmitted through SMS services.
					We are committed to ensuring that any SMS data collected through
					our website or services remains secure and confidential.
					Therefore, we do not share any SMS data outside of our
					organization, except where required by law or as necessary to
					provide the services requested by the user.
                  </p>
                  <p>
					Any SMS data collected is solely used for the purpose of
					delivering our services, improving user experience, and
					enhancing the functionality of our platform. We do not sell,
					rent, or disclose SMS data to third parties for marketing or
					any other purposes without explicit consent from the user.
					No mobile information will be shared with third parties/affiliates
					for marketing/promotional purposes. Information sharing to
					subcontractors in support services, such as customer service
					is permitted. All other use case categories exclude text
					messaging originator opt-in data and consent; this information
					will not be shared with any third parties.
                  </p>
                  <p>
					1. By providing your phone number for SMS services, you will
					receive updates regarding your service for Koala Insulation,
					including but not limited to, appointment confirmations,
					proposals, invoices and general communications.
                  </p>
                  <p> 
					2. You can cancel the SMS service at any time. Simply text
					"STOP" to the shortcode. Upon sending "STOP," we will confirm
					your unsubscribe status via SMS. Following this confirmation,
					you will no longer receive SMS messages from us. To rejoin,
					sign up as you did initially, and we will resume sending SMS
					messages to you.
                  </p>
                  <p>   
					3. If you experience issues with the messaging program, reply
					with the keyword HELP for more assistance, or reach out
					directly to your local OLP team. 
                  </p>
                  <p>   
					4. Carriers are not liable for delayed or undelivered messages.
                  </p>
                  <p>     
					5. As always, message and data rates may apply for messages
					sent to you from us and to us from you. You will receive text
					messages as often as needed. For questions about your text
					plan or data plan, contact your wireless provider.
                  </p>
                  <p>     					 
					For privacy-related inquiries, please refer to our privacy
					policy on this page. 
                  </p>
                  <p>     					   
					If you feel that we are not abiding by this privacy policy,
					you should contact us immediately via mail Attn: Privacy Officer, 445 West Drive, Melbourne, FL 32904
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div id="cta" class="brxe-template">
      <section id="brxe-agowsi" class="brxe-section section">
        <div id="brxe-hwktzs" class="brxe-block section-component">
          <div id="brxe-evndrw" class="brxe-block">
            <div id="brxe-tkgjbp" class="brxe-block">
              <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
                data-delay="0.2" data-duration="0.6">
                Find Your Location
              </h2>
              <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up" data-duration="0.6"
                data-delay="0.3">
                Ready to Improve Your Insulation?
              </div>
              <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                data-duration="0.6" data-delay="0.3">
                Whether it's spray foam insulation, blown in insulation, or
                anything in between, we're here to help.
              </div>
              <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                data-duration="0.6">
                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                  <input type="text" id="my-zipcode-input" class="top-zipcode-input" placeholder="Zip or Postal Code" />
                </div>
                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                  <div id="brxe-smmtik" class="brxe-div">
                    <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg" width="18"
                      height="18" viewBox="0 0 18 18" fill="none">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.65481 16.7633C8.67746 16.7764 8.69527 16.7865 8.70788 16.7936L8.72882 16.8053C8.89597 16.8971 9.10332 16.8964 9.27063 16.8056L9.29212 16.7936C9.30473 16.7865 9.32254 16.7764 9.34519 16.7633C9.39049 16.737 9.45523 16.6988 9.53663 16.6486C9.69935 16.5484 9.92906 16.4007 10.2035 16.2068C10.7513 15.8198 11.4823 15.2456 12.2149 14.4955C13.673 13.0026 15.1875 10.7596 15.1875 7.875C15.1875 4.45774 12.4173 1.6875 9 1.6875C5.58274 1.6875 2.8125 4.45774 2.8125 7.875C2.8125 10.7596 4.32699 13.0026 5.78509 14.4955C6.51769 15.2456 7.24868 15.8198 7.79654 16.2068C8.07094 16.4007 8.30065 16.5484 8.46337 16.6486C8.54477 16.6988 8.60951 16.737 8.65481 16.7633ZM9 10.125C10.2426 10.125 11.25 9.11764 11.25 7.875C11.25 6.63236 10.2426 5.625 9 5.625C7.75736 5.625 6.75 6.63236 6.75 7.875C6.75 9.11764 7.75736 10.125 9 10.125Z"
                        fill="white"></path>
                    </svg>
                  </div>
                  <div id="brxe-aymaue" class="brxe-text-basic">
                    Find My Location
                  </div>
                </div>
              </div>
            </div>
            <div id="brxe-bshupm" class="brxe-div">
              <img width="296" height="143"
                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug" decoding="async"
                data-type="string" />
            </div>
            <div id="brxe-wowmun" class="brxe-div">
              <img width="560" height="352"
                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf" decoding="async"
                data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
              " />
            </div>
            <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
              data-delay="1">
              <img width="440" height="410"
                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy" decoding="async"
                data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
              " />
            </div>
          </div>
        </div>
        <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6" data-delay="0.4">
          <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
            viewBox="0 0 62 62" fill="none">
            <g clip-path="url(#clip0_6431_678)">
              <path
                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                fill="#95C93D"></path>
            </g>
            <defs>
              <clipPath id="clip0_6431_678">
                <rect width="44" height="44" fill="white" transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
              </clipPath>
            </defs>
          </svg>
        </div>
      </section>
    </div>
    <section id="cta-quote" class="brxe-section section">
          <div id="brxe-bggdwc" class="brxe-block section-component">
            <div id="brxe-akzgbv" class="brxe-block">
              <div id="brxe-dckkyl" class="brxe-block">
                <h2 id="brxe-cfdbrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
                  data-duration="0.6">
                  Get a quote
                </h2>
                <div id="brxe-sushnb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                  data-delay="0.2" data-duration="0.6">
                  Ready to start your insulation project? Get a free quote from your
                  local Koala Insulation team today.
                </div>
                <div id="brxe-dbvghn" class="brxe-div btn is-no-icon"
                  data-interactions='[{"id":"pigxzw","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                  data-interaction-id="9f6f9b">
                  <div id="brxe-efxxnv" class="brxe-text-basic">
                    Get a Free Estimate
                  </div>
                </div>
              </div>
              <div id="brxe-ltloqb" class="brxe-div">
                <img width="296" height="143"
                  src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                  class="brxe-image image-contain css-filter size-full" alt="" id="brxe-bdqbqz" decoding="async"
                  loading="lazy" data-type="string" />
              </div>
              <div id="brxe-vjmidr" class="brxe-div">
                <img width="560" height="352"
                  src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                  class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-kuxsvn" decoding="async"
                  loading="lazy" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
            " />
              </div>
            </div>
          </div>
        </section>
  </main>
  <?php
}

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
  ?>
  <main id="brx-content">
    <section id="brxe-kikkzc" class="brxe-section section">
      <div id="brxe-wtsixe" class="brxe-container padding-global">
        <div id="brxe-isdpcy" class="brxe-block">
          <div id="brxe-hlomay" class="brxe-block section-component">
            <div id="brxe-osvhli" class="brxe-block">
              <div id="brxe-rvzcqy" class="brxe-block">
                <h2 id="brxe-qbsfxd" class="brxe-heading heading-style-h2 is-blue" data-animi="up" data-duration="0.6"
                  data-delay="0.2">
                  Privacy Policy
                </h2>
              </div>
              <div id="brxe-dllemy" class="brxe-text rich-text" data-animi="up" data-delay="0.3" data-duration="0.6">
                <div class="terms_grid">
                  <h3>1. Categories of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Koala Insulation Franchisor, LLC is the owner and operator
					of this website as well as the owner of Koala Insulation
					trademarks and associated identifiers. Koala Insulation
					Franchisor, LLC does not operate any insulation businesses.
					All insulation services are provided by independently owned
					and operated licensees or franchisees of the Koala Insulation
					brand under agreements governing such licenses and/or franchises.
					All information collected will be retained by Koala Insulation
					Franchisor, LLC and passed on to the appropriate franchisee or
					licensee or related company (collectively, “Affiliate”). 
					Information provided to Affiliates may also be provided to
					Koala Insulation Franchisor, LLC and will be retained in
					accordance with this policy and applicable law.
                  </p>
                  <p>
                    (b) Information You Provide to Us. When you place an order,
                    open an account, request a quote, participate in a
                    questionnaire, communicate with customer service or send us an
                    email, you provide us with information that we collect. Such
                    information may include your name, address, phone number,
                    credit card information, home or building information and
                    purchase history.
                  </p>
                  <p>
                    (c) Other Information We Receive and Store. We also receive
                    information from you as a result of your visiting our site,
                    interacting with our webpage or its links, sending us emails
                    including your IP address, your home or building data, your
                    email address, your browser type and version, your operating
                    system and platform, your purchase history, products you view,
                    products you search for, length of visits to pages and other
                    browser interaction information.
                  </p>
                  <p>
                    (d) All information provided by you to Koala Insulation or any
                    Affiliate is the property of the receiving party and/or Koala
					Insulation as governed by agreements between the Affiliate and
                    Koala Insulation. Once you voluntarily provide information to
                    us, we may retain it indefinitely and use it in the course of
                    our business.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>2. Use and Disclosure of Information We Collect.</h3>
                  <p></p>
                  <p>
                    (a) Use and Disclosure of Information. We will use and
                    disclose your information as follows: (1) to permit you to
                    review information online; (2) to analyze your needs and
                    eligibility for certain products or services that we or our
                    affiliates may offer; (3) to solicit you or subsequent owners
                    of your building or home for these services; (4) to ensure a
                    high quality customer experience; (5) to assist you with your
                    requested services; (6) to confirm and ship your orders or
                    schedule your estimate or install; (7) to follow up to make
                    sure such orders are fulfilled; (8) if, in the course of
                    placing an order and/or requesting information, you opt to
                    receive occasional emails from us notifying you of special
                    offers, we will use such information to notify you of such
                    offers; (9) if, in the course of placing an order and/or
                    entering a contest, you opt to receive occasional emails from
                    third parties to whom we provide your information, we will
                    disclose such information to such third parties; (10) to
                    protect the rights and safety of us, our shareholders,
                    members, officers, employees and customers; (11) at our
                    option, to notify you of any changes to this Privacy Policy or
                    our other terms and conditions of this website (you are bound
                    by the terms of any changes we post to our website whether or
                    not we notify you by email) and (12) as otherwise required by
                    law. We disclose your information to third parties only as
                    reasonably required to fulfill your orders and collect sums
                    due us (such as to shippers and credit card processors), to
                    protect the rights and safety of us, our shareholders,
                    members, employees and customers (such as to legal
                    representatives and law enforcement), as you expressly permit,
                    and as required by law (such as the result of a court
                    subpoena). We may transfer your information in the event of
                    the sale of substantially all of the assets of our business to
                    a third-party or in the event of a merger, consolidation or
                    acquisition. However, in such event, any acquirer will be
                    subject to the provisions of our commitments to you.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>3. Do Not Track.</h3>
                  <p></p>
                  <p>
                    We do not monitor, recognize, or respond to any opt-out or do
                    not track mechanisms, including general web browser “Do Not
                    Track” settings and/or signals. Further, we do not authorize
                    third parties to collect any personally identifiable
                    information about individuals who visit our site without
                    separate consent.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>4. Notification of Changes.</h3>
                  <p></p>
                  <p>
                    (a) Notification of Changes. Any changes in our Privacy Policy
                    will be posted to this website and will become effective as of
                    the date of posting with respect to information we then
                    collect in the future, but will not be changed with respect to
                    information that we have then already collected. It is and
                    will be your responsibility to review our Privacy Policy from
                    time to time to make sure you are aware of any changes.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>5. Effective Date.</h3>
                  <p></p>
                  <p>
                    (a) Effective Date. This Privacy Policy is effective with
                    respect to all data that we have collected since the date we
                    started collecting data, which was January 1, 2019.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>6. Questions.</h3>
                  <p></p>
                  <p>
                    (a) Questions. If you have any questions about our Privacy
                    Policy, feel free to contact us by sending a letter to: Koala
                    Insulation Franchisor, LLC ATTN: Privacy Manager, 445 West
					Drive, Melbourne, FL 32904.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
                  <h3>7. Limitation of Liability and Choice of Venue.</h3>
                  <p></p>
                  <p>
                    Under no circumstances shall the liability of any user
                    associated with this website exceed the amount the user has
                    paid to the website owner. Any dispute arising out of or
                    relating to this policy or any use or viewing of this website
                    shall be brought through arbitration in Brevard County,
                    Florida. In the event that this agreement to arbitrate is not
                    upheld, any court proceedings shall be in Brevard County
                    Florida and each user irrevocably consents and waives any
                    objections including without limitation objections to this
                    venue for forum non conveniens, in the courts having
                    jurisdiction over Brevard County Florida.
				  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>8. Information Use</h3>
                  <p></p>
                  <p>
					We use the collected information primarily for our own internal 
				    purposes, such as providing, maintaining, evaluating, and 
				    improving our services and Website, fulfilling requests for 
					information, and providing customer support.
                  </p>
                  <p>
					To Communicate with You: We use your contact information to 
					respond to your inquiries, provide legal services, send 
					administrative information, and keep you informed about 
					your case or our services.
                  </p>
                  <p>
					Marketing and Promotional Communications: With your consent, 
					we may use your information to send you updates, newsletters, 
					or marketing communications via email, phone, or text message. 
					You can opt out of receiving these communications at any time 
					by following the instructions provided in the communication or
					contacting us directly. 
                  </p>
                  <p>
					Legal Compliance: We may use your information to comply with
					applicable laws, regulations, or legal obligations, including
					responding to subpoenas, court orders, or legal requests.  
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>9. Security</h3>
                  <p></p>
                  <p>
					We follow generally accepted industry standards to protect the
					information submitted to us, both during transmission and once
					we receive it.
                  </p>
                  <p>
					If we collect sensitive information (such as credit card data),
					that information is encrypted and transmitted to us in a secure way.
					You can verify this by looking for a closed lock icon at the bottom
					of your web browser, or looking for “https” at the beginning of 
					the address of the web page.
                  </p>
                  <p>
					While we use encryption to protect sensitive information transmitted
					online, we also protect your information offline. Only employees
					who need the information to perform a specific job (for example,
					billing or customer service) are granted access to personally
					identifiable information. The computers/servers in which we store
					personally identifiable information are kept in a secure environment.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>10. Cookies</h3>
                  <p></p>
                  <p>
					We use “cookies” on this site. A cookie is a piece of data stored
					on a site visitor’s hard drive to help us improve your access to
					our site and identify repeat visitors to our site. For instance,
					when we use a cookie to identify you, you would not have to log
					in a password more than once, thereby saving time while on our site.
					Cookies can also enable us to track and target the interests of our
					users to enhance the experience on our site. Usage of a cookie is
					in no way linked to any personally identifiable information on our
					site. 
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>11. Sharing</h3>
                  <p></p>
                  <p>
					We do not sell or rent your personal information to third parties.
					We do not sell, rent, release, or transfer your SMS consent or
					phone number to any third party for any third party marketing
					purposes. We may share your information in the following
					circumstances:
                  </p>
                  <p>
					Service Providers: We may share your information with our 
					service providers who perform services on our behalf, 
					such as marketing, customer services, or technical support.
					These service providers are contractually obligated to 
					protect your information and use it only for services they
					provide.
                  </p>
                  <p>
					Legal Requirements: We may disclose your information if
					required by law, regulation, or legal process, or if we
					believe disclosure is necessary to protect our rights,
					property, or the safety of our users or others.
                  </p>
                  <p>
					We will disclose personal information and/or an IP address,
					when required by law or in the good-faith belief that such
					action is necessary to:
                  </p>
                  <p>
					Cooperate with the investigations of purported unlawful
					activities and conform to the edicts of the law or comply
					with legal process served on our company
                  </p>
                  <p>
					Protect and defend the rights or property of our Website
					and related properties
                  </p>
                  <p>
					Identify persons who may be violating the law, the rights
					of third parties, or otherwise misusing our Website or its
					related properties
                  </p>
                  <p>
					Please keep in mind that whenever you voluntarily disclose
					personal information online – for example through e-mail,
					discussion boards, or elsewhere – that information can be
					collected and used by others. In short, if you post personal
					information online that is accessible to the public, you may
					receive unsolicited messages from other parties in return.
                  </p>
                  <p>
					Ultimately, you are solely responsible for maintaining the
					secrecy of your personal information. Please be careful and
					responsible whenever you are online.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>12. Consent</h3>
                  <p></p>
                  <p>
					By using this Website, you consent to the collection and use
					of information as specified above. If we make changes to our
					Privacy Policy, we will post those changes on this page.
					Please review this page frequently to remain up-to-date with
					the information we collect, how we use it, and under what
					circumstances we disclose it. You must review the new Privacy
					Policy carefully to make sure you understand our practices 
					and procedures.
                  </p>
                  <p>
					You are not required to consent to receiving text messages
					from Koala Insulation. By providing your phone number and
					opting in, you consent to receive text messages from Koala
					Insulation regarding your inquiry, our services, or related
					legal matters. Message and data rates may apply. You can opt
					out of receiving text messages at any time by replying “STOP”
					to any text message you receive from us. Please note that
					opting out may limit our ability to communicate with you
					regarding your case or services.
                  </p>
                  <p>
					Opting Out: You may opt out of receiving marketing communications
					from us by following the instructions in those communications or
					contacting us directly. If you opt out, we may still send you 
					non-promotional communications related to your legal services
					or our ongoing business relationship.
                  </p>
                  <p>
					Access and Update Information: You have the right to access,
					update, or correct your personal information. To do so, please
					contact us using the information provided below. 
                  </p>
                  <p>
                    Message Frequency will vary from 1-10 messages in a week, pending your inquiry and service needs.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>13. Email and Data Collection</h3>
                  <p></p>
                  <p>
					By using our website and services, you consent to the collection,
					processing, and storage of SMS and email data as described in this
					privacy policy. We may collect SMS and email data for various
					purposes, including but not limited to:
                  </p>
                  <p>
					Communication: We may use SMS and email to communicate with users
					regarding account-related information, updates, announcements, and
					other relevant notifications.
                  </p>
                  <p>
					Service Delivery: SMS and email data may be collected to facilitate
					the delivery of our services, including account verification,
					password resets, transactional messages, and customer support.
                  </p>
                  <p>
					Marketing: With your explicit consent, we may use SMS and email
					to send promotional offers, newsletters, marketing campaigns,
					and other communications about our products or services. 
					You have the option to opt-out of marketing communications at 
					any time.
                  </p>
                  <p>
					Analytics: We may collect SMS and email data for analytical purposes
					to understand user behavior, improve our services, and personalize
					user experiences.
                  </p>
                  <p>
					We are committed to protecting the privacy and security of your SMS
					and email data. We do not share SMS and email data outside of our
					organization, except where required by law or as necessary to 
					provide the services requested by the user. Your SMS and email data
					is treated confidentially and securely, and we employ
					industry-standard measures to prevent unauthorized access, misuse,
					or alteration.
                  </p>
                  <p></p>
                </div>
                <div class="terms_grid">
				  <h3>14. SMS Data Handling</h3>
                  <p></p>
                  <p>
					We understand the importance of safeguarding the privacy of
					our users, including the data transmitted through SMS services.
					We are committed to ensuring that any SMS data collected through
					our website or services remains secure and confidential.
					Therefore, we do not share any SMS data outside of our
					organization, except where required by law or as necessary to
					provide the services requested by the user.
                  </p>
                  <p>
					Any SMS data collected is solely used for the purpose of
					delivering our services, improving user experience, and
					enhancing the functionality of our platform. We do not sell,
					rent, or disclose SMS data to third parties for marketing or
					any other purposes without explicit consent from the user.
					No mobile information will be shared with third parties/affiliates
					for marketing/promotional purposes. Information sharing to
					subcontractors in support services, such as customer service
					is permitted. All other use case categories exclude text
					messaging originator opt-in data and consent; this information
					will not be shared with any third parties.
                  </p>
                  <p>
					1. By providing your phone number for SMS services, you will
					receive updates regarding your service for Koala Insulation,
					including but not limited to, appointment confirmations,
					proposals, invoices and general communications.
                  </p>
                  <p> 
					2. You can cancel the SMS service at any time. Simply text
					"STOP" to the shortcode. Upon sending "STOP," we will confirm
					your unsubscribe status via SMS. Following this confirmation,
					you will no longer receive SMS messages from us. To rejoin,
					sign up as you did initially, and we will resume sending SMS
					messages to you.
                  </p>
                  <p>   
					3. If you experience issues with the messaging program, reply
					with the keyword HELP for more assistance, or reach out
					directly to your local OLP team. 
                  </p>
                  <p>   
					4. Carriers are not liable for delayed or undelivered messages.
                  </p>
                  <p>     
					5. As always, message and data rates may apply for messages
					sent to you from us and to us from you. You will receive text
					messages as often as needed. For questions about your text
					plan or data plan, contact your wireless provider.
                  </p>
                  <p>     					 
					For privacy-related inquiries, please refer to our privacy
					policy on this page. 
                  </p>
                  <p>     					   
					If you feel that we are not abiding by this privacy policy,
					you should contact us immediately via mail Attn: Privacy Officer, 445 West Drive, Melbourne, FL 32904
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div id="cta" class="brxe-template">
      <section id="brxe-agowsi" class="brxe-section section">
        <div id="brxe-hwktzs" class="brxe-block section-component">
          <div id="brxe-evndrw" class="brxe-block">
            <div id="brxe-tkgjbp" class="brxe-block">
              <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
                data-delay="0.2" data-duration="0.6">
                Find Your Location
              </h2>
              <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up" data-duration="0.6"
                data-delay="0.3">
                Ready to Improve Your Insulation?
              </div>
              <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                data-duration="0.6" data-delay="0.3">
                Whether it's spray foam insulation, blown in insulation, or
                anything in between, we're here to help.
              </div>
              <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                data-duration="0.6">
                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                  <input type="text" id="my-zipcode-input" class="top-zipcode-input" placeholder="Zip or Postal Code" />
                </div>
                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                  <div id="brxe-smmtik" class="brxe-div">
                    <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg" width="18"
                      height="18" viewBox="0 0 18 18" fill="none">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.65481 16.7633C8.67746 16.7764 8.69527 16.7865 8.70788 16.7936L8.72882 16.8053C8.89597 16.8971 9.10332 16.8964 9.27063 16.8056L9.29212 16.7936C9.30473 16.7865 9.32254 16.7764 9.34519 16.7633C9.39049 16.737 9.45523 16.6988 9.53663 16.6486C9.69935 16.5484 9.92906 16.4007 10.2035 16.2068C10.7513 15.8198 11.4823 15.2456 12.2149 14.4955C13.673 13.0026 15.1875 10.7596 15.1875 7.875C15.1875 4.45774 12.4173 1.6875 9 1.6875C5.58274 1.6875 2.8125 4.45774 2.8125 7.875C2.8125 10.7596 4.32699 13.0026 5.78509 14.4955C6.51769 15.2456 7.24868 15.8198 7.79654 16.2068C8.07094 16.4007 8.30065 16.5484 8.46337 16.6486C8.54477 16.6988 8.60951 16.737 8.65481 16.7633ZM9 10.125C10.2426 10.125 11.25 9.11764 11.25 7.875C11.25 6.63236 10.2426 5.625 9 5.625C7.75736 5.625 6.75 6.63236 6.75 7.875C6.75 9.11764 7.75736 10.125 9 10.125Z"
                        fill="white"></path>
                    </svg>
                  </div>
                  <div id="brxe-aymaue" class="brxe-text-basic">
                    Find My Location
                  </div>
                </div>
              </div>
            </div>
            <div id="brxe-bshupm" class="brxe-div">
              <img width="296" height="143"
                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug" decoding="async"
                data-type="string" />
            </div>
            <div id="brxe-wowmun" class="brxe-div">
              <img width="560" height="352"
                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf" decoding="async"
                data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
              " />
            </div>
            <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
              data-delay="1">
              <img width="440" height="410"
                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy" decoding="async"
                data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
              " />
            </div>
          </div>
        </div>
        <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6" data-delay="0.4">
          <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
            viewBox="0 0 62 62" fill="none">
            <g clip-path="url(#clip0_6431_678)">
              <path
                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                fill="#95C93D"></path>
            </g>
            <defs>
              <clipPath id="clip0_6431_678">
                <rect width="44" height="44" fill="white" transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
              </clipPath>
            </defs>
          </svg>
        </div>
      </section>
    </div>
    <section id="cta-quote" class="brxe-section section">
          <div id="brxe-bggdwc" class="brxe-block section-component">
            <div id="brxe-akzgbv" class="brxe-block">
              <div id="brxe-dckkyl" class="brxe-block">
                <h2 id="brxe-cfdbrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps" data-animi="up"
                  data-duration="0.6">
                  Get a quote
                </h2>
                <div id="brxe-sushnb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                  data-delay="0.2" data-duration="0.6">
                  Ready to start your insulation project? Get a free quote from your
                  local Koala Insulation team today.
                </div>
                <div id="brxe-dbvghn" class="brxe-div btn is-no-icon"
                  data-interactions='[{"id":"pigxzw","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                  data-interaction-id="9f6f9b">
                  <div id="brxe-efxxnv" class="brxe-text-basic">
                    Get a Free Estimate
                  </div>
                </div>
              </div>
              <div id="brxe-ltloqb" class="brxe-div">
                <img width="296" height="143"
                  src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                  class="brxe-image image-contain css-filter size-full" alt="" id="brxe-bdqbqz" decoding="async"
                  loading="lazy" data-type="string" />
              </div>
              <div id="brxe-vjmidr" class="brxe-div">
                <img width="560" height="352"
                  src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                  class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-kuxsvn" decoding="async"
                  loading="lazy" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
            " />
              </div>
            </div>
          </div>
        </section>
  </main>
  <?php
}
?>