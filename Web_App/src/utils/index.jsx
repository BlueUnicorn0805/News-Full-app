import slugify from "slugify";
import { store } from "../store/store";

export const getSiblings = function (elem) {
  let siblings = [];
  let sibling = elem.parentNode.firstChild;
  while (sibling) {
    if (sibling.nodeType === 1 && sibling !== elem) {
      siblings.push(sibling);
    }
    sibling = sibling.nextSibling;
  }
  return siblings;
};

export const getClosest = function (elem, selector) {
  for (; elem && elem !== document; elem = elem.parentNode) {
    if (elem.matches(selector)) return elem;
  }
  return null;
};

export const slideUp = (element, duration = 500) => {
  return new Promise(function (resolve) {
    element.style.height = element.offsetHeight + "px";
    element.style.transitionProperty = `height, margin, padding`;
    element.style.transitionDuration = duration + "ms";
    // element.offsetHeight;
    element.style.overflow = "hidden";
    element.style.height = 0;
    element.style.paddingTop = 0;
    element.style.paddingBottom = 0;
    element.style.marginTop = 0;
    element.style.marginBottom = 0;
    window.setTimeout(function () {
      element.style.display = "none";
      element.style.removeProperty("height");
      element.style.removeProperty("padding-top");
      element.style.removeProperty("padding-bottom");
      element.style.removeProperty("margin-top");
      element.style.removeProperty("margin-bottom");
      element.style.removeProperty("overflow");
      element.style.removeProperty("transition-duration");
      element.style.removeProperty("transition-property");
      resolve(false);
    }, duration);
  });
};

export const slideDown = (element, duration = 500) => {
  return new Promise(function () {
    element.style.removeProperty("display");
    let display = window.getComputedStyle(element).display;

    if (display === "none") display = "block";

    element.style.display = display;
    let height = element.offsetHeight;
    element.style.overflow = "hidden";
    element.style.height = 0;
    element.style.paddingTop = 0;
    element.style.paddingBottom = 0;
    element.style.marginTop = 0;
    element.style.marginBottom = 0;
    // element.offsetHeight;
    element.style.transitionProperty = `height, margin, padding`;
    element.style.transitionDuration = duration + "ms";
    element.style.height = height + "px";
    element.style.removeProperty("padding-top");
    element.style.removeProperty("padding-bottom");
    element.style.removeProperty("margin-top");
    element.style.removeProperty("margin-bottom");
    window.setTimeout(function () {
      element.style.removeProperty("height");
      element.style.removeProperty("overflow");
      element.style.removeProperty("transition-duration");
      element.style.removeProperty("transition-property");
    }, duration);
  });
};

export const slideToggle = (element, duration = 500) => {
  if (window.getComputedStyle(element).display === "none") {
    return slideDown(element, duration);
  } else {
    return slideUp(element, duration);
  }
};

// is login user check
export const isLogin = () => {
  let user = store.getState().user;
  if (user) {
    try {
      // user = JSON.parse(user);
      if (user.data.firebase_id) {
        return true;
      }
      return false;
    } catch (error) {
      return false;
    }
  }
  return false;
};

export const translate = (label, defaultLabel = null) => {
  /*Set default Label only if you want custom label */
  let langLabel =
    store.getState().languages.currentLanguageLabels.data &&
    store.getState().languages.currentLanguageLabels.data[label];
  if (langLabel) {
    return langLabel;
  } else {
    return !defaultLabel ? label : defaultLabel;
  }
};

// server image error
export const imgError = (e) => {
  e.target.src = "/images/no_image.jpeg";
};

// placholder image
export const placeholderImage = (e) => {
  e.target.src = "/images/placeholder.png";
};

// truncate text
export const truncateText = (text, characterLimit) => {
  if (text.length > characterLimit) {
    const truncatedText = text.substring(0, characterLimit) + "...";
    return truncatedText;
  }
  return text;
};

 // server image error
 export const profileimgError = (e) => {
  e.target.src = "/images/user.svg"
}

// slug
export const convertToSlug = (title) => {
  return slugify(title, {
    lower: true,
    strict: true,
  });
}

// minute read
export const calculateReadTime = (text) => {
  const wordsPerMinute = 200;
  const wordCount = text.trim().split(' ').length;
  const readTime = Math.ceil(wordCount / wordsPerMinute);
  return readTime;
};

export const extractTextFromHTML = (html) => {
  const doc = new DOMParser().parseFromString(html, "text/html");
  return doc.body.textContent || "";
};