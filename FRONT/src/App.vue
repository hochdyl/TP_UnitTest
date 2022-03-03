<template>
  <div>
    <component :is="currentView" />
  </div>
</template>

<script>
import HelloWorld from "./components/HelloWorld.vue";
import TheWelcome from "./components/TheWelcome.vue";
import NotFound from "./components/NotFound.vue";

const routes = {
  "": HelloWorld,
  comments: TheWelcome,
};

export default {
  data() {
    return {
      currentPath: window.location.hash,
    };
  },
  methods: {},
  created() {
    console.log(window.location.pathname.split("/"));
  },
  computed: {
    currentView() {
      return routes[window.location.pathname.split("/")[1] || ""] || NotFound;
    },
  },
  mounted() {
    window.addEventListener("hashchange", () => {
      this.currentPath = window.location.pathname;
    });
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
