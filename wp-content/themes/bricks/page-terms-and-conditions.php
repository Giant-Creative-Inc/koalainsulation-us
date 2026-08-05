<?php
/* Template Name: Terms and Conditions */
get_header();

// Get the current URL path
$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Split the URL into segments
$segments = explode('/', $current_url);

// Determine if the URL is location-specific or generic
if (count($segments) === 2 && $segments[1] === 'terms-and-conditions') {
    $location_slug = $segments[0];
    show_location_page($location_slug);

} elseif (count($segments) === 1 && $segments[0] === 'terms-and-conditions') {
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
        <section id="brxe-fcxrfo" class="brxe-section section">
            <div id="brxe-zwvzay" class="brxe-container padding-global">
                <div id="brxe-kkepxv" class="brxe-block">
                    <div id="brxe-ryokih" class="brxe-block section-component">
                        <div id="brxe-dgxrdf" class="brxe-block">
                            <div id="brxe-vhoaus" class="brxe-block">
                                <h2 id="brxe-ybaajy" class="brxe-heading heading-style-h2 is-blue" data-animi="up"
                                    data-duration="0.6" data-delay="0.2">
                                    Terms and Conditions of <?php echo $location_title ?>
                                </h2>
                            </div>
                            <div id="brxe-cdtfbt" class="brxe-text rich-text" data-animi="up" data-delay="0.3"
                                data-duration="0.6">
                                <div class="terms_grid">
                                    <p>
                                        Koala Insulation Franchisor, LLC (“Koala Insulation”, “Our” or “We”) is the owner and operator of this website, as well as the owner of Koala Insulation trademarks and associated identifiers. Koala Insulation does not operate any insulation businesses. All insulation services are provided by independently owned and operated licensees or franchisees of the Koala Insulation brand under agreements governing such licenses and/or franchises. All information collected will be retained by Koala Insulation and passed on to the appropriate franchisee or licensee or related company (collectively, “Affiliate”). Information provided to Affiliates may also be provided to Koala Insulation and will be retained in accordance with this policy and applicable law.
                                    </p>
                                    <p>
                                        Please read the following Terms carefully before using Koala Insulation’s web site, koalainsulation.com (“Website”). By accessing or using the Web Site, you agree to the following Terms. You should review these Terms regularly as they may change at any time at the sole discretion of Koala Insulation. If you do not agree to any portion of these Terms, you should not access or otherwise use the Web Site. “Content” refers to any text, materials, documents, images, graphics, logos, design, audio, video and any other information provided from or on, uploaded to and/or downloaded from the Web Site.
                                    </p>
                                    <p>
                                        We will make an effort to update this web page with any changes to these Terms and/or to the services described in these Terms and you are encouraged to review these Terms frequently (the date of the most recent revision to these Terms appear at the end of these Terms).
                                    </p>
                                    <p>
                                        Terms related to Online Account Access to your account(s) that you have with Koala Insulation (“Account”) is set forth in Paragraph 3 herein.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>
                                        1. CONVENIENCE AND INFORMATION ONLY; ACCEPTANCE OF TERMS.
                                    </h3>
                                    <p></p>
                                    <p>
                                        By merely providing access to the Web Site, Koala Insulation does not warrant or represent that: (a) the Content is accurate, complete, up-to-date or current; (b) Koala Insulation has any obligation to update any Content; (c) the Content is free from technical inaccuracies or typographical errors; (d) that the Content does not infringe on the intellectual property rights of any third party; (e) that the Content is free from changes caused by a third party; (f) your access to the Web Site will be free from interruptions, errors, computer viruses or other harmful components; and/or (g) any information obtained in response to questions asked through, or postings made on, the Web Site is accurate or complete. Your use of the Web Site and the services offered therein are subject to applicable Federal law and the laws of the State of Florida (“Applicable Law”).
                                    </p>
                                    <p>
                                        You affirm that you are either more than 18 years of age, or an emancipated minor, or possess legal parental or guardian consent, and are fully able and competent to enter into the terms, conditions, obligations, affirmations, representations, and warranties set forth in these Terms, and to abide by and comply with these Terms. In any case, you affirm that you are over the age of 13, as THE WEB SITE IS NOT INTENDED FOR CHILDREN UNDER 13 THAT ARE UNACCOMPANIED BY HIS OR HER PARENT OR LEGAL GUARDIAN.
                                    </p>
                                    <p><strong>&nbsp;</strong></p>
                                </div>
                                <div class="terms_grid">
                                    <h3>2. SITE USE AND CONTENT.</h3>
                                    <p></p>
                                    <p>
                                        You may view, copy or print pages from the Web Site solely for personal, non-commercial purposes. You may not otherwise use, modify, copy, print, display, reproduce, distribute or publish any information from the Web Site without the express, prior, written consent of Koala Insulation. At any time, we may, without further notice, make changes to the Web Site, to these Terms and/or to the services described in these Terms.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>3. DISCLAIMERS.</h3>
                                    <p></p>
                                    <p>
                                        (a) NO WARRANTIES; INDEMNIFICATION. YOU EXPRESSLY AGREE THAT YOUR USE OF THE WEB SITE IS AT YOUR SOLE RISK. THE WEB SITE, THE ONLINE SERVICE AND THE CONTENT IS PROVIDED “AS IS” AND “AS AVAILABLE” FOR YOUR USE, WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, UNLESS SUCH WARRANTIES ARE LEGALLY INCAPABLE OF EXCLUSION. KOALA INSULATION PROVIDES THE WEB SITE AND THE ONLINE SERVICE ON A COMMERCIALLY REASONABLE BASIS AND KOALA INSULATION MAKES NO REPRESENTATIONS OR WARRANTIES THAT THE WEB SITE, THE ONLINE SERVICE, THE CONTENT OR ANY SERVICES OFFERED IN CONNECTION WITH THE WEB SITE ARE OR SHALL REMAIN UNINTERRUPTED OR ERROR-FREE, THE CONTENT SHALL BE NON-INFRINGING ON ANY THIRD PARTY’S INTELLECTUAL PROPERTY RIGHTS, THAT DEFECTS SHALL BE CORRECTED, THAT THE WEB PAGES ON THE WEB SITE, THE ONLINE SERVICE, ANY ELECTRONIC COMMUNICATION OR THE SERVERS USED IN CONNECTION WITH THE WEB SITE ARE OR SHALL REMAIN FREE FROM ANY VIRUSES, WORMS, TIME BOMBS, DROP DEAD DEVICES, TROJAN HORSES OR OTHER HARMFUL COMPONENTS, OR THAT ANY PERSON USING THE WEB SITE WILL BE THE PERSON THAT HE OR SHE REPRESENTS HIMSELF OR HERSELF TO BE. KOALA INSULATION DOES NOT GUARANTEE THAT YOU WILL BE ABLE TO ACCESS OR USE THE WEB SITE AND/OR THE ONLINE SERVICE AT TIMES OR LOCATIONS OF YOUR CHOOSING, OR THAT KOALA INSULATION SHALL HAVE ADEQUATE CAPACITY FOR THE WEB SITE AND/OR THE ONLINE SERVICE AS A WHOLE OR IN ANY SPECIFIC GEOGRAPHIC AREA.
                                    </p>
                                    <p>
                                        (b) INDEMNIFICATION. You agree to defend, indemnify and hold Koala Insulation and its affiliates, subsidiaries, owners, directors, officers, employees and agents harmless from and against any and all claims, demands, suits, proceedings, liabilities, judgments, losses, damages, expenses and costs (including without limitation reasonable attorneys’ fees) assessed or incurred by Koala Insulation, directly or indirectly, with respect to or arising out of: (i) your failure to comply with these Terms; (ii) your breach of your obligations under these Terms; (iii) your use of the rights granted hereunder, including without limitation any claims made by any third parties; and/or (iv) your violation of any third party right, including without limitation any copyright, property, or privacy right.
                                    </p>
                                    <p>
                                        (c) NOT INVESTMENT ADVICE. KOALA INSULATION DOES NOT INTEND TO PROVIDE ANY INVESTMENT ADVICE OR INFORMATION RELATING TO ITSELF OR ANY KOALA INSULATION IDENTIFIED ON THE WEB SITE. Nevertheless, the Web Site may, from time to time, contain information on the current or prospective financial condition of this and/or certain other companies. Koala Insulation cautions that there are various important factors that could cause actual results to differ materially from those indicated in the information you may encounter on the Web Site. Accordingly, there can be no assurance that such indicated results will be realized. These factors include, among other things, legislative and regulatory initiatives regarding regulation of American companies doing business abroad; political and economic conditions and developments in the United States and in foreign countries in which the companies discussed on the Web Site operate; financial market conditions and the results of financing efforts; and changes in commodity prices and interest rates.
                                    </p>
                                    <p>
                                        (d) NOT AN OFFER TO SELL A FRANCHISE. UNLESS YOU RECEIVE A LETTER FROM KOALA INSULATION OFFERING YOUR APPROVAL TO ENTER INTO THE KOALA INSULATION FRANCHISE SYSTEM YOU HAVE NOT BEEN OFFERED THE OPPORTUNITY TO PURCHASE A FRANCHISE AND NOTHING ON THIS WEBSITE OR ANY OTHER COMMUNICATION PROVIDED TO YOU, INCLUDING WITHOUT LIMITATION THE FRANCHISE DISCLOSURE DOCUMENT (“FDD”) CONSTITUTES AN OFFER CAPABLE OF ACCEPTANCE. CERTAIN STATES MAY REQUIRE THE REGISTRATION OR EXEMPTION FROM REGISTRATION OF FRANCHISE OPPORTUNITITES. TO THE EXTENT THAT KOALA INSULATION HAS NOT SO REGISTERED, IT DOES NOT AND CANNOT OFFER YOU A FRANCHISE. INFORMATION ON THIS WEBSITE AND IN OTHER COMMUNICATIONS MAY NOT BE RELIED UPON – YOU MUST MAKE YOUR OWN DECISIONS BASED UPON YOUR OWN DUE DILIGENCE. TO THE EXTENT THAT YOU HAVE RECEIVED AN FDD, DO NOT RELY ON ANY INFORMATION NOT CONTAINED WITHIN THAT DOCUMENT.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>4. LIMITATION OF LIABILITY.</h3>
                                    <p></p>
                                    <p>
                                        KOALA INSULATION’S ENTIRE LIABILITY AND YOUR EXCLUSIVE REMEDY WITH RESPECT TO THE USE OF THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE SHALL BE THE DELETION OF YOUR USER DATA WITH KOALA INSULATION. IN NO EVENT WILL KOALA INSULATION BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, PUNITIVE OR CONSEQUENTIAL DAMAGES ARISING FROM YOUR USE OF THE WEB SITE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, OR FOR ANY OTHER CLAIM RELATED IN ANY WAY TO YOUR USE OF THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, INCLUDING, BUT NOT LIMITED TO, (A) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (B) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, (C) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR COMPUTER SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (D) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, AND/OR (E) ANY VIRUSES, WORMS, TIME BOMBS, DROP DEAD DEVICES, TROJAN HORSES OR OTHER HARMFUL COMPONENTS THAT MAY BE TRANSMITTED TO OR THROUGH THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE BY ANY THIRD PARTY OR FOR ANY LOSS OR DAMAGE OF ANY KIND. BECAUSE SOME STATES OR JURISDICTIONS DO NOT ALLOW THE EXCLUSION OR LIMITATION OF LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL DAMAGES, IN SUCH STATES OR JURISDICTIONS KOALA INSULATION’S LIABILITY WILL BE LIMITED TO THE GREATEST EXTENT PERMITTED BY LAW.

                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>5. PRIVACY.</h3>
                                    <p></p>
                                    <p>
                                        Personal data that you provide regarding yourself will be handled in accordance with Koala Insulation’s Privacy Policy located at <a
                                            href="https://www.koalainsulation.com/privacy-policy">www.koalainsulation.com/privacy-policy</a>.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>6. THIRD PARTY CONTENT.</h3>
                                    <p></p>
                                    <p>
                                        (a) Koala Insulation may provide hyperlinks to other web sites maintained by third parties, or Koala Insulation may provide third party content on the Web Site by framing or other methods. THE LINKS TO THIRD PARTY WEB SITES ARE PROVIDED FOR YOUR CONVENIENCE AND INFORMATION ONLY. THE CONTENT ON ANY LINKED WEB SITE IS NOT UNDER KOALA INSULATION’S CONTROL AND KOALA INSULATION IS NOT RESPONSIBLE FOR THE CONTENT OF LINKED WEB SITES, INCLUDING ANY FURTHER LINKS CONTAINED IN A THIRD PARTY WEB SITE. IF YOU DECIDE TO ACCESS ANY OF THE THIRD PARTY WEB SITES LINKED TO THE WEB SITE, YOU DO SO ENTIRELY AT YOUR OWN RISK.
                                    </p>
                                    <p>
                                        (b) If a third party links to the Web Site, it is not necessarily an indication of an endorsement, authorization, sponsorship, affiliation, joint venture or partnership by or with Koala Insulation. In most cases, Koala Insulation is not even aware that a third party has linked to the Web Site. A web site that links to the Web Site: (i) may link to, but not replicate, Koala Insulation’s Content; (ii) may not create a browser, border environment or frame Koala Insulation’s Content; (iii) may not imply that Koala Insulation is endorsing it or its products; (iv) may not misrepresent its relationship with Koala Insulation; (v) may not present false or misleading information about Koala Insulation’s products or services; and (vi) should not include content that could be construed as distasteful, offensive or controversial, and should contain only Content that is appropriate for all age groups.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>7. COPYRIGHT AND TRADEMARKS.</h3>
                                    <p></p>
                                    <p>
                                        The trademarks, service marks and logos used and displayed on the Web Site are property of Koala Insulation, or its subsidiaries or affiliates. Koala Insulation is the copyright owner or authorized licensee of all text and all graphics contained on the Web Site. All trademarks and service marks of Koala Insulation that may be referred to on the Web Site are the property of Koala Insulation. Other parties’ trademarks and service marks that may be referred to on the Web Site are the property of their respective owners. Nothing on the Web Site should be construed as granting, by implication, estoppel or otherwise, any license or right to use any of Koala Insulation’s trademarks or service marks without Koala Insulation’s  prior written permission. Koala Insulation aggressively enforces its intellectual property rights. Neither the name of Koala Insulation nor any of Koala Insulation’s  other trademarks, service marks or copyrighted materials may be used in any way, including in any advertising, hyperlink, publicity or promotional materials of any kind, whether relating to the Web Site or otherwise, without Koala Insulation’s prior written permission, except that a third party web site that desires to link to the Web Site and that complies with the requirements of Paragraph 6(b) above may use the name “Koala Insulation” in or as part of that URL link. If you believe that any Content on the Web Site violates any intellectual property right of yours, please contact Koala Insulation at the address, email address or telephone number set forth at the bottom of these Terms.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>8. LOCAL LAWS.</h3>
                                    <p></p>
                                    <p>
                                        Koala Insulation makes no representation that content or materials in the Web Site are appropriate or available for use in jurisdictions outside the United States. Access to the Web Site from jurisdictions where such access is illegal is prohibited. If you choose to access the Web Site from other jurisdictions, you do so on your own initiative and are responsible for compliance with applicable local laws. Koala Insulation is not responsible for any violation of law by You or anyone acting on your behalf. You may not use or export the Content or materials in the Web Site in violation of U.S. export laws and regulations. You agree that the Web Site, these Terms and the Online Service shall be interpreted and governed in accordance with Applicable Law. The Web Site and the Online Service shall be deemed a passive website and service that does not give rise to personal jurisdiction over Koala Insulation, either specific or general, in jurisdictions other than the states covered by the preceding sentence. You agree and hereby submit to the exclusive personal jurisdiction of the Federal and State  courts located in Brevard County, Florida. You further agree to comply with all applicable laws regarding the transmission of technical data exported from the United States and the country in which you reside (if different from the United States).
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>9. AVAILABILITY.</h3>
                                    <p></p>
                                    <p>
                                        Information that Koala Insulation publishes in the Web Site may contain references or cross-references to products, programs or services of Koala Insulation that are not necessarily announced or available in your area. Such references do not mean that Koala Insulation will announce any of those products, programs or services in your area at any time in the future. You should contact Koala Insulation for information regarding the products, programs and services that may be available to you, if any.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>10. NON-TRANSFERABILITY OF USER ACCOUNT.</h3>
                                    <p></p>
                                    <p>
                                        User Accounts and UserIDs are non-transferable, and all users are obligated to take preventative measures to prohibit unauthorized users from accessing the Web Site with his or her UserID and password. You may not assign these Terms, in whole or in part, or delegate any of your responsibilities hereunder to any third party. Any such attempted assignment or delegation will not be recognized by Koala Insulation unless acknowledged and approved by Koala Insulation  in writing. Koala Insulation has no obligation to provide you with written acknowledgment or approval. Koala Insulation may, at any time and in its sole discretion, assign these Terms, in whole or in part, or delegate any of our rights and responsibilities under these Terms to any third party or entity.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>11. TERMINATION OF SERVICE.</h3>
                                    <p></p>
                                    <p>
                                       We may terminate your User Account or right to access secured portions of the Web Site at any time, without notice, for conduct that we, in our sole and absolute discretion, believe violates these Terms and/or is harmful to other users of the Web Site, to Koala Insulation, to the business of the Web Site’s Internet service provider, or to other information providers.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>12. CUSTOMER COMMENTS.</h3>
                                    <p></p>
                                    <p>
                                        We welcome the submission of comments, information or feedback through the Web Site. By submitting information through the Web Site, you agree that the information submitted shall be subject to the Koala Insulation Web Site Privacy Policy located at <a
                                            href="https://www.koalainsulation.com/privacy-policy">www.koalainsulation.com/privacy-policy</a>.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>13. MISCELLANEOUS.</h3>
                                    <p></p>
                                    <p>
                                        If any provision of these Terms is deemed invalid by a Court of competent jurisdiction, the invalidity of such provision shall not affect the validity of the remaining provisions of these Terms, which shall remain in full force and effect. No waiver of any term of these Terms shall be deemed a further or continuing waiver of such term or any other term, and Koala Insulation’s failure to assert any right or provision under these Terms shall not constitute a waiver of such right or provision. These Terms and the Koala Insulation Web Site Privacy Policy located at www.koalainsulation.com/privacy-policy are the entire agreement between you and Koala Insulation with respect to your use of the Web Site and the Online Service, and supersede any and all prior communications and prior agreements, whether written or oral, between you and Koala Insulation regarding the Web Site and the Online Service.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                  <h3>14. SMS DATA HANDLING.</h3>
                                  <p>We understand the importance of safeguarding the privacy of our users, including the data transmitted through SMS services. We are committed to ensuring that any SMS data collected through our website or services remains secure and confidential. Therefore, we do not share any SMS data outside of our organization, except where required by law or as necessary to provide the services requested by the user.</p>
                                  <p></p>
                                  <p>Any SMS data collected is solely used for the purpose of delivering our services, improving user experience, and enhancing the functionality of our platform. We do not sell, rent, or disclose SMS data to third parties for marketing or any other purposes without explicit consent from the user. No mobile information will be shared with third parties/affiliates for marketing/promotional purposes. Information sharing to subcontractors in support services, such as customer service is permitted. All other use case categories exclude text messaging originator opt-in data and consent; this information will not be shared with any third parties.</p>
                                  <p></p>
                                  <ol>
                                    <li>By providing your phone number for SMS services, you will receive updates regarding your service for Koala Insulation, including but not limited to, appointment confirmations, proposals, invoices and general communications.</li>
                                    <li>You can cancel the SMS service at any time. Simply text "STOP" to the shortcode. Upon sending "STOP," we will confirm your unsubscribe status via SMS. Following this confirmation, you will no longer receive SMS messages from us. To rejoin, sign up as you did initially, and we will resume sending SMS messages to you.</li>
                                    <li>If you experience issues with the messaging program, reply with the keyword HELP for more assistance, or reach out directly to your local OLP team.</li>
                                    <li>Carriers are not liable for delayed or undelivered messages.</li>
                                    <li>As always, message and data rates may apply for messages sent to you from us and to us from you. You will receive text messages as often as needed. For questions about your text plan or data plan, contact your wireless provider.</li>
                                  </ol>
                                </div>
                                <div class="terms_grid">
                                    <h3 class="text-center" style="text-align: center">
                                        Your Consent To This Agreement
                                    </h3>
                                    <p>&nbsp;</p>
                                    <p>
                                        By accessing and using the Web Site, you consent to and agree to be bound by the foregoing Terms. If we decide to change these Terms, we will make a reasonable effort to post those changes on the web page so that you will always be able to understand the terms and conditions that apply to your use of the Web Site and/or the Online Service. Your use of the Web Site and/or the Online Service following any amendment of these Terms will signify your assent to and acceptance of its revised terms.
                                    </p>
                                    <p>
                                        If you have additional questions or comments of any kind, or if you see anything on the Web Site that you think is inappropriate, please let us know by sending your comments to:
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        Koala Insulation Franchisor, LLC
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        Attn: General Counsel
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        445 West Drive
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        Melbourne, FL 32904
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        EFFECTIVE AS OF: January 1, 2026
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        LAST UPDATED: February 3, 2026
                                    </p>
                                    <p class="text-center">&nbsp;</p>
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
                            <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                data-animi="up" data-delay="0.2" data-duration="0.6">
                                Find Your Location
                            </h2>
                            <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up"
                                data-duration="0.6" data-delay="0.3">
                                Ready to Improve Your Insulation?
                            </div>
                            <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold"
                                data-animi="up" data-duration="0.6" data-delay="0.3">
                                Whether it's spray foam insulation, blown in insulation, or
                                anything in between, we're here to help.
                            </div>
                            <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                                data-duration="0.6">
                                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                                    <input type="text" id="my-zipcode-input" class="top-zipcode-input"
                                        placeholder="Zip or Postal Code" />
                                </div>
                                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                                    <div id="brxe-smmtik" class="brxe-div">
                                        <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg"
                                            width="18" height="18" viewBox="0 0 18 18" fill="none">
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
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug"
                                decoding="async" data-type="string" />
                        </div>
                        <div id="brxe-wowmun" class="brxe-div">
                            <img width="560" height="352"
                                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf"
                                decoding="async" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
              " />
                        </div>
                        <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
                            data-delay="1">
                            <img width="440" height="410"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy"
                                decoding="async" data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
              " />
                        </div>
                    </div>
                </div>
                <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6"
                    data-delay="0.4">
                    <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
                        viewBox="0 0 62 62" fill="none">
                        <g clip-path="url(#clip0_6431_678)">
                            <path
                                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                                fill="#95C93D"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_6431_678">
                                <rect width="44" height="44" fill="white"
                                    transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
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
        <section id="brxe-fcxrfo" class="brxe-section section">
            <div id="brxe-zwvzay" class="brxe-container padding-global">
                <div id="brxe-kkepxv" class="brxe-block">
                    <div id="brxe-ryokih" class="brxe-block section-component">
                        <div id="brxe-dgxrdf" class="brxe-block">
                            <div id="brxe-vhoaus" class="brxe-block">
                                <h2 id="brxe-ybaajy" class="brxe-heading heading-style-h2 is-blue" data-animi="up"
                                    data-duration="0.6" data-delay="0.2">
                                    Terms and Conditions
                                </h2>
                            </div>
                            <div id="brxe-cdtfbt" class="brxe-text rich-text" data-animi="up" data-delay="0.3"
                                data-duration="0.6">
                                <div class="terms_grid">
                                    <p>
                                        Koala Insulation Franchisor, LLC (“Koala Insulation”, “Our” or “We”) is the owner and operator of this website, as well as the owner of Koala Insulation trademarks and associated identifiers. Koala Insulation does not operate any insulation businesses. All insulation services are provided by independently owned and operated licensees or franchisees of the Koala Insulation brand under agreements governing such licenses and/or franchises. All information collected will be retained by Koala Insulation and passed on to the appropriate franchisee or licensee or related company (collectively, “Affiliate”). Information provided to Affiliates may also be provided to Koala Insulation and will be retained in accordance with this policy and applicable law.
                                    </p>
                                    <p>
                                        Please read the following Terms carefully before using Koala Insulation’s web site, koalainsulation.com (“Website”). By accessing or using the Web Site, you agree to the following Terms. You should review these Terms regularly as they may change at any time at the sole discretion of Koala Insulation. If you do not agree to any portion of these Terms, you should not access or otherwise use the Web Site. “Content” refers to any text, materials, documents, images, graphics, logos, design, audio, video and any other information provided from or on, uploaded to and/or downloaded from the Web Site.
                                    </p>
                                    <p>
                                        We will make an effort to update this web page with any changes to these Terms and/or to the services described in these Terms and you are encouraged to review these Terms frequently (the date of the most recent revision to these Terms appear at the end of these Terms).
                                    </p>
                                    <p>
                                        Terms related to Online Account Access to your account(s) that you have with Koala Insulation (“Account”) is set forth in Paragraph 3 herein.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>
                                        1. CONVENIENCE AND INFORMATION ONLY; ACCEPTANCE OF TERMS.
                                    </h3>
                                    <p></p>
                                    <p>
                                        By merely providing access to the Web Site, Koala Insulation does not warrant or represent that: (a) the Content is accurate, complete, up-to-date or current; (b) Koala Insulation has any obligation to update any Content; (c) the Content is free from technical inaccuracies or typographical errors; (d) that the Content does not infringe on the intellectual property rights of any third party; (e) that the Content is free from changes caused by a third party; (f) your access to the Web Site will be free from interruptions, errors, computer viruses or other harmful components; and/or (g) any information obtained in response to questions asked through, or postings made on, the Web Site is accurate or complete. Your use of the Web Site and the services offered therein are subject to applicable Federal law and the laws of the State of Florida (“Applicable Law”).
                                    </p>
                                    <p>
                                        You affirm that you are either more than 18 years of age, or an emancipated minor, or possess legal parental or guardian consent, and are fully able and competent to enter into the terms, conditions, obligations, affirmations, representations, and warranties set forth in these Terms, and to abide by and comply with these Terms. In any case, you affirm that you are over the age of 13, as THE WEB SITE IS NOT INTENDED FOR CHILDREN UNDER 13 THAT ARE UNACCOMPANIED BY HIS OR HER PARENT OR LEGAL GUARDIAN.
                                    </p>
                                    <p><strong>&nbsp;</strong></p>
                                </div>
                                <div class="terms_grid">
                                    <h3>2. SITE USE AND CONTENT.</h3>
                                    <p></p>
                                    <p>
                                        You may view, copy or print pages from the Web Site solely for personal, non-commercial purposes. You may not otherwise use, modify, copy, print, display, reproduce, distribute or publish any information from the Web Site without the express, prior, written consent of Koala Insulation. At any time, we may, without further notice, make changes to the Web Site, to these Terms and/or to the services described in these Terms.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>3. DISCLAIMERS.</h3>
                                    <p></p>
                                    <p>
                                        (a) NO WARRANTIES; INDEMNIFICATION. YOU EXPRESSLY AGREE THAT YOUR USE OF THE WEB SITE IS AT YOUR SOLE RISK. THE WEB SITE, THE ONLINE SERVICE AND THE CONTENT IS PROVIDED “AS IS” AND “AS AVAILABLE” FOR YOUR USE, WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, UNLESS SUCH WARRANTIES ARE LEGALLY INCAPABLE OF EXCLUSION. KOALA INSULATION PROVIDES THE WEB SITE AND THE ONLINE SERVICE ON A COMMERCIALLY REASONABLE BASIS AND KOALA INSULATION MAKES NO REPRESENTATIONS OR WARRANTIES THAT THE WEB SITE, THE ONLINE SERVICE, THE CONTENT OR ANY SERVICES OFFERED IN CONNECTION WITH THE WEB SITE ARE OR SHALL REMAIN UNINTERRUPTED OR ERROR-FREE, THE CONTENT SHALL BE NON-INFRINGING ON ANY THIRD PARTY’S INTELLECTUAL PROPERTY RIGHTS, THAT DEFECTS SHALL BE CORRECTED, THAT THE WEB PAGES ON THE WEB SITE, THE ONLINE SERVICE, ANY ELECTRONIC COMMUNICATION OR THE SERVERS USED IN CONNECTION WITH THE WEB SITE ARE OR SHALL REMAIN FREE FROM ANY VIRUSES, WORMS, TIME BOMBS, DROP DEAD DEVICES, TROJAN HORSES OR OTHER HARMFUL COMPONENTS, OR THAT ANY PERSON USING THE WEB SITE WILL BE THE PERSON THAT HE OR SHE REPRESENTS HIMSELF OR HERSELF TO BE. KOALA INSULATION DOES NOT GUARANTEE THAT YOU WILL BE ABLE TO ACCESS OR USE THE WEB SITE AND/OR THE ONLINE SERVICE AT TIMES OR LOCATIONS OF YOUR CHOOSING, OR THAT KOALA INSULATION SHALL HAVE ADEQUATE CAPACITY FOR THE WEB SITE AND/OR THE ONLINE SERVICE AS A WHOLE OR IN ANY SPECIFIC GEOGRAPHIC AREA.
                                    </p>
                                    <p>
                                        (b) INDEMNIFICATION. You agree to defend, indemnify and hold Koala Insulation and its affiliates, subsidiaries, owners, directors, officers, employees and agents harmless from and against any and all claims, demands, suits, proceedings, liabilities, judgments, losses, damages, expenses and costs (including without limitation reasonable attorneys’ fees) assessed or incurred by Koala Insulation, directly or indirectly, with respect to or arising out of: (i) your failure to comply with these Terms; (ii) your breach of your obligations under these Terms; (iii) your use of the rights granted hereunder, including without limitation any claims made by any third parties; and/or (iv) your violation of any third party right, including without limitation any copyright, property, or privacy right.
                                    </p>
                                    <p>
                                        (c) NOT INVESTMENT ADVICE. KOALA INSULATION DOES NOT INTEND TO PROVIDE ANY INVESTMENT ADVICE OR INFORMATION RELATING TO ITSELF OR ANY KOALA INSULATION IDENTIFIED ON THE WEB SITE. Nevertheless, the Web Site may, from time to time, contain information on the current or prospective financial condition of this and/or certain other companies. Koala Insulation cautions that there are various important factors that could cause actual results to differ materially from those indicated in the information you may encounter on the Web Site. Accordingly, there can be no assurance that such indicated results will be realized. These factors include, among other things, legislative and regulatory initiatives regarding regulation of American companies doing business abroad; political and economic conditions and developments in the United States and in foreign countries in which the companies discussed on the Web Site operate; financial market conditions and the results of financing efforts; and changes in commodity prices and interest rates.
                                    </p>
                                    <p>
                                        (d) NOT AN OFFER TO SELL A FRANCHISE. UNLESS YOU RECEIVE A LETTER FROM KOALA INSULATION OFFERING YOUR APPROVAL TO ENTER INTO THE KOALA INSULATION FRANCHISE SYSTEM YOU HAVE NOT BEEN OFFERED THE OPPORTUNITY TO PURCHASE A FRANCHISE AND NOTHING ON THIS WEBSITE OR ANY OTHER COMMUNICATION PROVIDED TO YOU, INCLUDING WITHOUT LIMITATION THE FRANCHISE DISCLOSURE DOCUMENT (“FDD”) CONSTITUTES AN OFFER CAPABLE OF ACCEPTANCE. CERTAIN STATES MAY REQUIRE THE REGISTRATION OR EXEMPTION FROM REGISTRATION OF FRANCHISE OPPORTUNITITES. TO THE EXTENT THAT KOALA INSULATION HAS NOT SO REGISTERED, IT DOES NOT AND CANNOT OFFER YOU A FRANCHISE. INFORMATION ON THIS WEBSITE AND IN OTHER COMMUNICATIONS MAY NOT BE RELIED UPON – YOU MUST MAKE YOUR OWN DECISIONS BASED UPON YOUR OWN DUE DILIGENCE. TO THE EXTENT THAT YOU HAVE RECEIVED AN FDD, DO NOT RELY ON ANY INFORMATION NOT CONTAINED WITHIN THAT DOCUMENT.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>4. LIMITATION OF LIABILITY.</h3>
                                    <p></p>
                                    <p>
                                        KOALA INSULATION’S ENTIRE LIABILITY AND YOUR EXCLUSIVE REMEDY WITH RESPECT TO THE USE OF THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE SHALL BE THE DELETION OF YOUR USER DATA WITH KOALA INSULATION. IN NO EVENT WILL KOALA INSULATION BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, PUNITIVE OR CONSEQUENTIAL DAMAGES ARISING FROM YOUR USE OF THE WEB SITE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, OR FOR ANY OTHER CLAIM RELATED IN ANY WAY TO YOUR USE OF THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, INCLUDING, BUT NOT LIMITED TO, (A) ERRORS, MISTAKES, OR INACCURACIES OF CONTENT, (B) PERSONAL INJURY OR PROPERTY DAMAGE, OF ANY NATURE WHATSOEVER, RESULTING FROM YOUR ACCESS TO AND USE OF THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, (C) ANY UNAUTHORIZED ACCESS TO OR USE OF OUR COMPUTER SERVERS AND/OR ANY AND ALL PERSONAL INFORMATION AND/OR FINANCIAL INFORMATION STORED THEREIN, (D) ANY INTERRUPTION OR CESSATION OF TRANSMISSION TO OR FROM THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE, AND/OR (E) ANY VIRUSES, WORMS, TIME BOMBS, DROP DEAD DEVICES, TROJAN HORSES OR OTHER HARMFUL COMPONENTS THAT MAY BE TRANSMITTED TO OR THROUGH THE WEB SITE, THE ONLINE SERVICE AND/OR ANY SERVICE PROVIDED IN CONNECTION WITH THE WEB SITE BY ANY THIRD PARTY OR FOR ANY LOSS OR DAMAGE OF ANY KIND. BECAUSE SOME STATES OR JURISDICTIONS DO NOT ALLOW THE EXCLUSION OR LIMITATION OF LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL DAMAGES, IN SUCH STATES OR JURISDICTIONS KOALA INSULATION’S LIABILITY WILL BE LIMITED TO THE GREATEST EXTENT PERMITTED BY LAW.

                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>5. PRIVACY.</h3>
                                    <p></p>
                                    <p>
                                        Personal data that you provide regarding yourself will be handled in accordance with Koala Insulation’s Privacy Policy located at <a
                                            href="https://www.koalainsulation.com/privacy-policy">www.koalainsulation.com/privacy-policy</a>.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>6. THIRD PARTY CONTENT.</h3>
                                    <p></p>
                                    <p>
                                        (a) Koala Insulation may provide hyperlinks to other web sites maintained by third parties, or Koala Insulation may provide third party content on the Web Site by framing or other methods. THE LINKS TO THIRD PARTY WEB SITES ARE PROVIDED FOR YOUR CONVENIENCE AND INFORMATION ONLY. THE CONTENT ON ANY LINKED WEB SITE IS NOT UNDER KOALA INSULATION’S CONTROL AND KOALA INSULATION IS NOT RESPONSIBLE FOR THE CONTENT OF LINKED WEB SITES, INCLUDING ANY FURTHER LINKS CONTAINED IN A THIRD PARTY WEB SITE. IF YOU DECIDE TO ACCESS ANY OF THE THIRD PARTY WEB SITES LINKED TO THE WEB SITE, YOU DO SO ENTIRELY AT YOUR OWN RISK.
                                    </p>
                                    <p>
                                        (b) If a third party links to the Web Site, it is not necessarily an indication of an endorsement, authorization, sponsorship, affiliation, joint venture or partnership by or with Koala Insulation. In most cases, Koala Insulation is not even aware that a third party has linked to the Web Site. A web site that links to the Web Site: (i) may link to, but not replicate, Koala Insulation’s Content; (ii) may not create a browser, border environment or frame Koala Insulation’s Content; (iii) may not imply that Koala Insulation is endorsing it or its products; (iv) may not misrepresent its relationship with Koala Insulation; (v) may not present false or misleading information about Koala Insulation’s products or services; and (vi) should not include content that could be construed as distasteful, offensive or controversial, and should contain only Content that is appropriate for all age groups.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>7. COPYRIGHT AND TRADEMARKS.</h3>
                                    <p></p>
                                    <p>
                                        The trademarks, service marks and logos used and displayed on the Web Site are property of Koala Insulation, or its subsidiaries or affiliates. Koala Insulation is the copyright owner or authorized licensee of all text and all graphics contained on the Web Site. All trademarks and service marks of Koala Insulation that may be referred to on the Web Site are the property of Koala Insulation. Other parties’ trademarks and service marks that may be referred to on the Web Site are the property of their respective owners. Nothing on the Web Site should be construed as granting, by implication, estoppel or otherwise, any license or right to use any of Koala Insulation’s trademarks or service marks without Koala Insulation’s  prior written permission. Koala Insulation aggressively enforces its intellectual property rights. Neither the name of Koala Insulation nor any of Koala Insulation’s  other trademarks, service marks or copyrighted materials may be used in any way, including in any advertising, hyperlink, publicity or promotional materials of any kind, whether relating to the Web Site or otherwise, without Koala Insulation’s prior written permission, except that a third party web site that desires to link to the Web Site and that complies with the requirements of Paragraph 6(b) above may use the name “Koala Insulation” in or as part of that URL link. If you believe that any Content on the Web Site violates any intellectual property right of yours, please contact Koala Insulation at the address, email address or telephone number set forth at the bottom of these Terms.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>8. LOCAL LAWS.</h3>
                                    <p></p>
                                    <p>
                                        Koala Insulation makes no representation that content or materials in the Web Site are appropriate or available for use in jurisdictions outside the United States. Access to the Web Site from jurisdictions where such access is illegal is prohibited. If you choose to access the Web Site from other jurisdictions, you do so on your own initiative and are responsible for compliance with applicable local laws. Koala Insulation is not responsible for any violation of law by You or anyone acting on your behalf. You may not use or export the Content or materials in the Web Site in violation of U.S. export laws and regulations. You agree that the Web Site, these Terms and the Online Service shall be interpreted and governed in accordance with Applicable Law. The Web Site and the Online Service shall be deemed a passive website and service that does not give rise to personal jurisdiction over Koala Insulation, either specific or general, in jurisdictions other than the states covered by the preceding sentence. You agree and hereby submit to the exclusive personal jurisdiction of the Federal and State  courts located in Brevard County, Florida. You further agree to comply with all applicable laws regarding the transmission of technical data exported from the United States and the country in which you reside (if different from the United States).
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>9. AVAILABILITY.</h3>
                                    <p></p>
                                    <p>
                                        Information that Koala Insulation publishes in the Web Site may contain references or cross-references to products, programs or services of Koala Insulation that are not necessarily announced or available in your area. Such references do not mean that Koala Insulation will announce any of those products, programs or services in your area at any time in the future. You should contact Koala Insulation for information regarding the products, programs and services that may be available to you, if any.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>10. NON-TRANSFERABILITY OF USER ACCOUNT.</h3>
                                    <p></p>
                                    <p>
                                        User Accounts and UserIDs are non-transferable, and all users are obligated to take preventative measures to prohibit unauthorized users from accessing the Web Site with his or her UserID and password. You may not assign these Terms, in whole or in part, or delegate any of your responsibilities hereunder to any third party. Any such attempted assignment or delegation will not be recognized by Koala Insulation unless acknowledged and approved by Koala Insulation  in writing. Koala Insulation has no obligation to provide you with written acknowledgment or approval. Koala Insulation may, at any time and in its sole discretion, assign these Terms, in whole or in part, or delegate any of our rights and responsibilities under these Terms to any third party or entity.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>11. TERMINATION OF SERVICE.</h3>
                                    <p></p>
                                    <p>
                                      We may terminate your User Account or right to access secured portions of the Web Site at any time, without notice, for conduct that we, in our sole and absolute discretion, believe violates these Terms and/or is harmful to other users of the Web Site, to Koala Insulation, to the business of the Web Site’s Internet service provider, or to other information providers.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>12. CUSTOMER COMMENTS.</h3>
                                    <p></p>
                                    <p>
                                        We welcome the submission of comments, information or feedback through the Web Site. By submitting information through the Web Site, you agree that the information submitted shall be subject to the Koala Insulation Web Site Privacy Policy located at <a
                                            href="https://www.koalainsulation.com/privacy-policy">www.koalainsulation.com/privacy-policy</a>.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                    <h3>13. MISCELLANEOUS.</h3>
                                    <p></p>
                                    <p>
                                        If any provision of these Terms is deemed invalid by a Court of competent jurisdiction, the invalidity of such provision shall not affect the validity of the remaining provisions of these Terms, which shall remain in full force and effect. No waiver of any term of these Terms shall be deemed a further or continuing waiver of such term or any other term, and Koala Insulation’s failure to assert any right or provision under these Terms shall not constitute a waiver of such right or provision. These Terms and the Koala Insulation Web Site Privacy Policy located at www.koalainsulation.com/privacy-policy are the entire agreement between you and Koala Insulation with respect to your use of the Web Site and the Online Service, and supersede any and all prior communications and prior agreements, whether written or oral, between you and Koala Insulation regarding the Web Site and the Online Service.
                                    </p>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="terms_grid">
                                  <h3>14. SMS DATA HANDLING.</h3>
                                  <p>We understand the importance of safeguarding the privacy of our users, including the data transmitted through SMS services. We are committed to ensuring that any SMS data collected through our website or services remains secure and confidential. Therefore, we do not share any SMS data outside of our organization, except where required by law or as necessary to provide the services requested by the user.</p>
                                  <p></p>
                                  <p>Any SMS data collected is solely used for the purpose of delivering our services, improving user experience, and enhancing the functionality of our platform. We do not sell, rent, or disclose SMS data to third parties for marketing or any other purposes without explicit consent from the user. No mobile information will be shared with third parties/affiliates for marketing/promotional purposes. Information sharing to subcontractors in support services, such as customer service is permitted. All other use case categories exclude text messaging originator opt-in data and consent; this information will not be shared with any third parties.</p>
                                  <p></p>
                                  <ol>
                                    <li>By providing your phone number for SMS services, you will receive updates regarding your service for Koala Insulation, including but not limited to, appointment confirmations, proposals, invoices and general communications.</li>
                                    <li>You can cancel the SMS service at any time. Simply text "STOP" to the shortcode. Upon sending "STOP," we will confirm your unsubscribe status via SMS. Following this confirmation, you will no longer receive SMS messages from us. To rejoin, sign up as you did initially, and we will resume sending SMS messages to you.</li>
                                    <li>If you experience issues with the messaging program, reply with the keyword HELP for more assistance, or reach out directly to your local OLP team.</li>
                                    <li>Carriers are not liable for delayed or undelivered messages.</li>
                                    <li>As always, message and data rates may apply for messages sent to you from us and to us from you. You will receive text messages as often as needed. For questions about your text plan or data plan, contact your wireless provider.</li>
                                  </ol>
                                </div>
                                <div class="terms_grid">
                                    <h3 class="text-center" style="text-align: center">
                                        Your Consent To This Agreement
                                    </h3>
                                    <p>&nbsp;</p>
                                    <p>
                                        By accessing and using the Web Site, you consent to and agree to be bound by the foregoing Terms. If we decide to change these Terms, we will make a reasonable effort to post those changes on the web page so that you will always be able to understand the terms and conditions that apply to your use of the Web Site and/or the Online Service. Your use of the Web Site and/or the Online Service following any amendment of these Terms will signify your assent to and acceptance of its revised terms.
                                    </p>
                                    <p>
                                        If you have additional questions or comments of any kind, or if you see anything on the Web Site that you think is inappropriate, please let us know by sending your comments to:
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        Koala Insulation Franchisor, LLC
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        Attn: General Counsel
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        445 West Drive
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        Melbourne, FL 32904
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        EFFECTIVE AS OF: January 1, 2026
                                    </p>
                                    <p class="text-center" style="text-align: center">
                                        LAST UPDATED: February 3, 2026
                                    </p>
                                    <p class="text-center">&nbsp;</p>
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
                            <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                data-animi="up" data-delay="0.2" data-duration="0.6">
                                Find Your Location
                            </h2>
                            <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up"
                                data-duration="0.6" data-delay="0.3">
                                Ready to Improve Your Insulation?
                            </div>
                            <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold"
                                data-animi="up" data-duration="0.6" data-delay="0.3">
                                Whether it's spray foam insulation, blown in insulation, or
                                anything in between, we're here to help.
                            </div>
                            <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                                data-duration="0.6">
                                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                                    <input type="text" id="my-zipcode-input" class="top-zipcode-input"
                                        placeholder="Zip or Postal Code" />
                                </div>
                                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                                    <div id="brxe-smmtik" class="brxe-div">
                                        <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg"
                                            width="18" height="18" viewBox="0 0 18 18" fill="none">
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
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug"
                                decoding="async" data-type="string" />
                        </div>
                        <div id="brxe-wowmun" class="brxe-div">
                            <img width="560" height="352"
                                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf"
                                decoding="async" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
                <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
              " />
                        </div>
                        <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
                            data-delay="1">
                            <img width="440" height="410"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy"
                                decoding="async" data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
                <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
              " />
                        </div>
                    </div>
                </div>
                <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6"
                    data-delay="0.4">
                    <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
                        viewBox="0 0 62 62" fill="none">
                        <g clip-path="url(#clip0_6431_678)">
                            <path
                                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                                fill="#95C93D"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_6431_678">
                                <rect width="44" height="44" fill="white"
                                    transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
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