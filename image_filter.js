/**************************************************
 * Image Filter Class Javascript Function
 *
 * @package PHP Image Filter GD
 * @version 2.0
 * @author MT Jordan <mtjo62@gmail.com>
 * @copyright 2026
 * @license MIT
 *************************************************/

let imgUrl = "imageFilter.php";

window.addEventListener("load", filterImage(), false);

function filterImage() {
    const imgfilter = document.getElementsByClassName("filter-gd");

    for (i = 0; i < imgfilter.length; i++) {
      const imgEffect = imgfilter[i].getAttribute("data-filter")
      const imgSrc = imgfilter[i].getAttribute("src");
      imgfilter[i].setAttribute("src", imgUrl + "?file=" + imgSrc + "&filter=" + imgEffect);
    }
}

/* EOF image_filter.js */
/* Location: ./image_filter.js */
