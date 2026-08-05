document.addEventListener("DOMContentLoaded", function () {
    const servicesList = document.getElementById("usa-our-services");
    const servicesListCa = document.getElementById("ca-our-services");

    if (servicesList) {
        const servicesItems = servicesList.querySelectorAll(".service-item");

        servicesItems.forEach((item, index) => {
            if (index % 2 !== 0) {
                item.style.flexDirection = "row-reverse";
            }
        });
    }

    if (servicesListCa) {
        const servicesItems = servicesListCa.querySelectorAll(".service-item");

        servicesItems.forEach((item, index) => {
            if (index % 2 !== 0) {
                item.style.flexDirection = "row-reverse";
            }
        });
    }

    /*const serviceLinks = document.querySelectorAll(".service-link-tag");

    serviceLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();

        const targetId = this.id;
        const targetItem = document.querySelector(
          `.service-item[id="${targetId}"]`
        );

        if (targetItem) {
          const stickyNavHeight =
            document.getElementById("brx-header").offsetHeight;

          const targetPosition =
            targetItem.getBoundingClientRect().top +
            window.pageYOffset -
            stickyNavHeight;

          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          });
        }
      });
    });*/
});