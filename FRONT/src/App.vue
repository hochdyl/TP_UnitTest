<template>
  <div>
    <component :is="currentView" />
  </div>
</template>

<script>
import Posts from "./components/Posts.vue";
import Comments from "./components/Comments.vue";
import FormComment from "./components/FormComment.vue";
import NotFound from "./components/NotFound.vue";

const routes = {
  "": Posts,
  comments: Comments,
  addcomment: FormComment,
};

export default {
  data() {
    return {};
  },
  methods: {},
  created() {},
  computed: {
    currentView() {
      return routes[window.location.pathname.split("/")[1] || ""] || NotFound;
    },
  },
  mounted() {
    window.addEventListener("hashchange", () => {
      this.currentPath = window.location.pathname;
    });
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://kit.fontawesome.com/04afb75ea3.js"
    );
    recaptchaScript.setAttribute("crossorigin", "anonymous");
    document.head.appendChild(recaptchaScript);
  },
};
</script>

<style>
@import "./assets/base.css";

header {
  line-height: 1.5;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

a,
.green {
  text-decoration: none;
  color: hsla(160, 100%, 37%, 1);
  transition: 0.4s;
}
</style>
