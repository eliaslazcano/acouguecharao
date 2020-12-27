<template>
  <v-app :dark="$store.state.dark">
    <v-navigation-drawer
      app
      clipped
      v-if="$store.getters['session/logged']"
      v-model="showMenu"
    ></v-navigation-drawer>
    <v-app-bar
      app
      dark
      clipped-left
      v-if="$store.getters['session/logged']"
      :color="$store.state.dark ? 'secondary' : 'primary'"
    >
      <v-app-bar-nav-icon @click.stop="showMenu = !showMenu" />
      <v-toolbar-title class="ml-0 pl-4">{{$vuetify.breakpoint.lgAndUp ? config.appBarTitle : config.appBarTitleMobile}}</v-toolbar-title>
      <v-spacer/>
      <v-menu
        left
        bottom
      >
        <template v-slot:activator="{ on: menu, attrs }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on: tooltip }">
              <v-btn icon v-on="{ ...menu, ...tooltip }" v-bind="attrs">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <span>Opções</span>
          </v-tooltip>
        </template>
        <v-list dense>
          <v-list-item :to="'/usuario/' + $store.getters['session/payload'].id">
            <v-list-item-content>
              <v-list-item-title class="option-item">
                <v-icon class="mr-2">mdi-account</v-icon>Minha conta
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item to="/usuarios">
            <v-list-item-content>
              <v-list-item-title class="option-item">
                <v-icon class="mr-2">mdi-account-multiple</v-icon>Colaboradores
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item @click="exit">
            <v-list-item-content>
              <v-list-item-title class="option-item red--text">
                <v-icon class="mr-2" color="red">mdi-power-standby</v-icon>Sair
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-main>
      <transition name="fade-transition" @after-leave="scrollTop" mode="out-in">
        <router-view/>
      </transition>
    </v-main>
    <g-snackbar/>
    <g-alert/>
    <g-loader/>
  </v-app>
</template>

<script>
//TODO - Menu Lateral
import GSnackbar from '@/components/global/GSnackbar';
import GAlert from '@/components/global/GAlert';
import GLoader from '@/components/global/GLoader';
import StringHelper from '@/helpers/StringHelper';
import { config } from '@/config';
import { version } from '../package.json';
export default {
  name: 'App',
  components: {GLoader, GAlert, GSnackbar},
  data: () => ({
    showMenu: null,
    config,
  }),
  methods: {
    scrollTop() {
      window.scrollTo(0, 0);
    },
    exit() {
      this.showMenu = false;
      this.$store.dispatch('session/destroy');
    },
  },
  computed: {
    shortName() {
      const name = this.$store.getters['session/payload'] ? this.$store.getters['session/payload'].name : '';
      return name ? StringHelper.shortName(name.toUpperCase()) : '';
    },
    nameInitials() {
      return this.shortName ? StringHelper.nameInitials(this.shortName) : '';
    },
    occupation() {
      return this.$store.getters['session/payload'] ? this.$store.getters['session/payload'].occupation.toUpperCase() : '';
    },
    version() {
      const array = version.split('.');
      return array[0] + '.' + array[1];
    },
  }
}
</script>
