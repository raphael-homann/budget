<template>
  <v-progress-linear v-if="loading" indeterminate color="primary" ></v-progress-linear>
  <v-app v-if="!loading">
    <v-app-bar>
      <template v-slot:prepend>
        <v-app-bar-nav-icon @click="toggleSideBar"></v-app-bar-nav-icon>
      </template>
      <v-toolbar-title>{{ t('budget.app.title') }}</v-toolbar-title>
    </v-app-bar>

    <v-navigation-drawer color="secondary" v-if="sidebar" expand-on-hover rail permanent>
      <v-list-item prepend-icon="mdi-home" to="/budget" :title="t('budget.menu.home')"></v-list-item>
      <v-divider></v-divider>
<!--      <v-list-item prepend-icon="mdi-currency-eur" to="/budget/workflow" :title="t('budget.menu.workflow')"></v-list-item>-->
<!--      <v-list-item prepend-icon="mdi-format-list-checkbox" to="/budget/todos" :title="t('budget.menu.todos')"></v-list-item>-->
      <template v-slot:append>
        <v-list :items="languages" @click:select="selectLanguage"></v-list>
      </template>
    </v-navigation-drawer>

    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>


<script lang="ts">
import {useLocale} from "vuetify";
import {defineComponent} from 'vue'

import Budget from "./Data/Entity/Budget";
import {store} from "./service/store";

const App = defineComponent({
  name: "App",
  data(){
    return {
      sidebar: true,
      loading: false,
      languages: [
        {title: 'Français', value: 'fr'},
        {title: 'English', value: 'en'},
      ]
    }
  },

  setup() {
    const { t } = useLocale()
    return {
      t
    }
  },
  computed: {
  },


  methods: {
    toggleSideBar() {
      this.sidebar = !this.sidebar;
    },
    selectLanguage(locale: any) {
      this.$vuetify.locale.current = locale.id
    },
    loadData() {
      store.entityManager.load('/budget-data/full').then((data: any) => {
        console.log(store.entityManager.getRepository(Budget).getItems().map((item: Budget) => item.name));
      });
    },
    mount() {
      store.appData = window.appData
      this.loadData();
    },
    unmount() {
    },
  },
  watch: {
  },

  mounted() {
    this.unmount();
    this.mount();
  },
  unmounted() {
    this.unmount();
  },

});

export default App;
</script>
