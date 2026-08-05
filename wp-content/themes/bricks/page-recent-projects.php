<?php
/* Template Name: Recent Projects */
get_header();

// Get the current URL to extract the location slug
$current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Strip query string (e.g. NitroPack params) before parsing
$location_slug = trim(str_replace('/recent-projects', '', $current_url), '/'); // Remove '/recent-projects' from the URL to get the location slug

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location'); // Replace 'location' with your custom post type slug for location

if ($location) {
  // Get the location ID
  $location_id = $location->ID;

  // Query for "Recent Projects" posts related to the current location
  $args = array(
    'post_type' => 'resources-landing-pa', // The custom post type slug for "Recent Projects"
    'posts_per_page' => -1, // Show all posts
    'meta_query' => array(
      array(
        'key' => 'rl_related_location', // The ACF relationship field key in "Recent Projects"
        'value' => $location_id, // The location ID from the URL
        'compare' => 'LIKE', // Ensure it checks for the location ID in the relationship field
      ),
    ),
    'tax_query' => array(
      array(
        'taxonomy' => 'resources-page-type',
        'field' => 'slug',
        'terms' => 'recent-projects', // Replace with the term you want to filter by
      ),
    ),
  );

  $query = new WP_Query($args);

  if ($query->have_posts()):
    while ($query->have_posts()):
      $query->the_post();

      // Check if NiceJob integration is enabled
      $use_niceJob = get_field('use_nice_job');
      if ($use_niceJob) {

        // Get NiceJob fields
        $rl_niceJob_embed = get_field('rl_nicejob_embed');
        $rl_niceJob_csv = get_field('rl_nicejob_csv');
        $rl_niceJob_img_url = get_field('rl_nicejob_img_url');

        // Include custom styles for the map
        echo '<style>#map {height: 100vh;width: 100%;}/* Slider base */.slider-content {position: relative;width: 300px;height: 200px;overflow: hidden;margin: auto;border-radius: 8px;box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);}.slider-content img {display: none;width: 100%;height: 100%;object-fit: cover;border-radius: 8px;}.slider-content img.active {display: block;}.content-bottom {padding: .5rem 1rem;}.location {display: flex;align-items: center;gap: 10px;}.arrow {position: absolute;top: 100px;font-size: 18px;/* color: white; *//* background-color: rgba(0, 0, 0, 0.5); */background-color: white;color: black;padding: 8px;cursor: pointer;border: none;border-radius: 50%;z-index: 10;width: 30px;height: 30px;display: flex;align-items: center;justify-content: center;}.arrow-left {left: 10px;}.arrow-right {right: 10px;}h4 {margin: 15px 0 10px 0;color: white;padding: 0 10px;font-size: 14px;}p {color: white;padding: 0 10px 10px 10px;font-size: 12px;margin: 0;}/* Estilo de la InfoWindow */.gm-style-iw-d {overflow: hidden !important;}.gm-style-iw.gm-style-iw-c {background: linear-gradient(133.15deg,#3f6088 35.97%,#283962 81.55%,#1c253e 120.38%);padding: 0 !important;border-radius: 8px !important;}.gm-style-iw-chr span {background: white !important;}.gm-style-iw-ch {display: none;}.gm-style-iw-chr {position: absolute;z-index: 10;right: 0;}.hidden {display: none !important;}</style>';
      }
      ?>
      <main id="brx-content">
        <section id="brxe-toxqeb" class="brxe-section section">
          <div id="brxe-ykpymv" class="brxe-container padding-global">
              <div id="brxe-ezcjwt" class="brxe-block section-component">
                <div id="brxe-iyzntd" class="brxe-block">
                    <div id="brxe-qwenkp" class="brxe-block hero_content-wrapper">
                      <div id="brxe-ixvlsr" class="brxe-div image-wrapper absolute"><img width="572" height="214" src="https://koalastage.wpenginepowered.com/wp-content/uploads/2024/06/Vector.png" class="brxe-image image-contain css-filter size-full" alt="" id="brxe-trylcc" decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="https://koalastage.wpenginepowered.com/wp-content/uploads/2024/06/Vector.png 572w, https://koalastage.wpenginepowered.com/wp-content/uploads/2024/06/Vector-300x112.png 300w"></div>
                      <h2 id="brxe-mqypve" class="brxe-heading heading-style-h2 font-weight-bold is-all-caps" data-animi="up" data-duration="0.6" style="text-align: left; max-width: 532px;"><?php the_title(); ?></h2>
                      <div id="brxe-phxlqj" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up" data-duration="0.6" data-delay="0.2">
                        <?php
                        $heroContent = get_field('rl_hero_description');
                        if ($heroContent) {
                          echo '<div style="text-align: left;">' . esc_html($heroContent) . '</div>';
                        }
                        ?>
                      </div>
                    <?php $rl_image = wp_get_attachment_image_url(get_field('rl_image'), 'full');
                      if ($rl_image): ?>
                        </div>
                            <div id="brxe-vdwwag" class="brxe-block hero_image-wrapper">
                              <img width="1024" height="952" src="<?php echo esc_url($rl_image); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="Recent Projects" id="brxe-xpkvbi" loading="eager"
                                decoding="async" srcset="<?php echo esc_url($rl_image); ?>" sizes="(max-width: 1024px) 100vw, 1024px" />
                            </div>
                        </div>
                    <?php endif; ?>
              </div>
          </div>
        </section>
        <section id="brxe-wpwydx" class="brxe-section section">
  <div id="brxe-uebymw" class="brxe-container padding-global">
    <div id="brxe-ifwoob" class="brxe-block section-component">
      <div id="brxe-sqbaqq" class="brxe-block" data-animi="up" data-duration="0.6">
        <div id="brxe-vqlorg" class="brxe-text">
          <?php
          $content = get_field('rl_description');
          if ($content) { echo $content; }
          ?>
          
          <?php if ($use_niceJob) : ?>
            <div id="map" style="max-width: 100%; width:1500px;"></div>
            
            <!-- PapaParse is safe to load as it doesn't conflict with Google -->
            <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>

            <script>
            (function() {
              let nj_map;
              let nj_activeInfoWindow = null;
              const csv_file = "https://koalainsulation.com/?secure_csv=reviews";
              const img_base = "<?php echo get_field('rl_nicejob_img_url'); ?>";

              // 1. WATCHER: Instead of loading the API again, wait for NiceJob/RealLabs to load it
              function startMapWatcher() {
                if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                  initNiceJobMap();
                } else {
                  // Check again in 500ms
                  setTimeout(startMapWatcher, 500);
                }
              }

              async function initNiceJobMap() {
                nj_map = new google.maps.Map(document.getElementById("map"), {
                  zoom: 11,
                  center: { lat: 26.35, lng: -80.15 },
                });

                try {
                  const response = await fetch(csv_file);
                  const text = await response.text();
                  const data = Papa.parse(text, { header: true, delimiter: ";" }).data;
                  const puntos = data.filter(p => p.Latitude && p.Longitude && p.Name);

                  puntos.forEach((p, index) => {
                    const marker = new google.maps.Marker({
                      position: { lat: parseFloat(p.Latitude), lng: parseFloat(p.Longitude) },
                      map: nj_map,
                      title: p.Name,
                      icon: {
                        url: "https://koalainsulation.com/wp-content/uploads/icon.svg",
                        scaledSize: new google.maps.Size(40, 40),
                      },
                    });

                    // Reduced extension list to minimize 404 noise
                    async function fetchValidImages() {
                      const extensions = ["jpg", "webp"]; // Most common
                      const rawImages = [p.Image1, p.Image2, p.Image3].filter(Boolean);
                      const valid = [];

                      for (const name of rawImages) {
                        for (const ext of extensions) {
                          const url = `${img_base}${name}.${ext}`;
                          try {
                            const res = await fetch(url, { method: "HEAD" });
                            if (res.ok) { valid.push(url); break; }
                          } catch (e) {}
                        }
                      }
                      return valid.length ? valid : ["https://koalainsulation.com/wp-content/uploads/recent-projects/north-broward-boca/img-default.png"];
                    }

                    fetchValidImages().then((imgs) => {
                      const sliderId = `nj_slider_${index}`;
                      const info = new google.maps.InfoWindow({
                        content: `
                        <div style="text-align:left; max-width:300px; position:relative;">
                          <div id="${sliderId}" class="slider-content">${imgs.map(src => `<img src="${src}">`).join("")}</div>
                          ${imgs.length > 1 ? `
                            <button class="arrow arrow-left" onclick="moveNjSlider('${sliderId}', -1)">&#10094;</button>
                            <button class="arrow arrow-right" onclick="moveNjSlider('${sliderId}', 1)">&#10095;</button>
                          ` : ''}
                          <div class="content-bottom">
                            <h4>${p.Name}</h4>
                            <p>${p.Review || ""}</p>
                            <p class="location">${p.City_FL_ZIP || ""}</p>
                          </div>
                        </div>`
                      });

                      marker.addListener("click", () => {
                        if (nj_activeInfoWindow) nj_activeInfoWindow.close();
                        info.open(nj_map, marker);
                        nj_activeInfoWindow = info;
                        setTimeout(() => {
                          const slider = document.getElementById(sliderId);
                          if (slider && slider.querySelector('img')) {
                            slider.querySelector('img').classList.add('active');
                          }
                        }, 200);
                      });
                    });
                  });
                } catch (error) { console.error("CSV error:", error); }
              }

              // Global slider function for the onclick events
              window.moveNjSlider = function(sliderId, direction) {
                const slider = document.getElementById(sliderId);
                const slides = slider.querySelectorAll("img");
                let activeIndex = Array.from(slides).findIndex(img => img.classList.contains("active"));
                if (activeIndex === -1) activeIndex = 0;
                
                slides[activeIndex].classList.remove("active");
                const nextIndex = (activeIndex + direction + slides.length) % slides.length;
                slides[nextIndex].classList.add("active");
              };

              // Start checking for Google Maps
              startMapWatcher();
            })();
            </script>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

        <div style="display: none">
          <?php
          $rl_code = get_field('rl_reallabs_embed');
          if ($rl_code) {
            echo $rl_code;
          }
          ?>
        </div>
      </main>
      <?php
    endwhile;
    echo '</div>';
  else:
    echo '<p>No related post found for this location.</p>';
  endif;

  wp_reset_postdata(); // Reset the query
} else {
  echo '<p>Location not found.</p>';
}

get_footer();
?>