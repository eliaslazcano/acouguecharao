<template>
  <v-app :dark="$store.state.dark">
    <v-navigation-drawer
      app
      clipped
      v-if="$store.getters['session/logged']"
      v-model="showMenu"
    >
      <template v-slot:prepend>
        <v-list>
          <v-list-item two-line :to="'/usuario/' + $store.getters['session/payload'].id">
            <v-list-item-avatar>
              <v-avatar color="primary" class="font-weight-medium white--text elevation-2">
                <!-- Use <img> here -->
                <template>{{nameInitials}}</template>
              </v-avatar>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>{{shortName}}</v-list-item-title>
              <v-list-item-subtitle>{{occupation}}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </template>
      <v-divider/>
      <v-list dense>
        <v-list-item link to="/">
          <v-list-item-action>
            <v-icon>mdi-home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Início</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/caixa/vendas">
          <v-list-item-action>
            <v-icon>mdi-cash-register</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Caixa</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/produtos">
          <v-list-item-action>
            <v-icon>mdi-tag</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Produtos</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

      </v-list>
      <template v-slot:append>
        <div class="px-2 pb-3" v-if="!$vuetify.breakpoint.lgAndUp">
          <v-btn
            @click="exit"
            color="primary"
            class="white--text"
            block
          >
            <v-icon class="mr-1">mdi-power-standby</v-icon>SAIR
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>
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
      if (!this.$store.getters['session/payload']) return '';
      if (this.$store.getters['session/payload'].shortname) return this.$store.getters['session/payload'].shortname.toUpperCase();
      if (!this.$store.getters['session/payload'].name) return '';
      return StringHelper.shortName(this.$store.getters['session/payload'].name.toUpperCase());
    },
    nameInitials() {
      return this.shortName ? StringHelper.nameInitials(this.shortName) : '';
    },
    occupation() {
      if (!this.$store.getters['session/payload']) return '';
      if (this.$store.getters['session/payload'].occupation) return this.$store.getters['session/payload'].occupation.toUpperCase();
      else return '';
    },
    version() {
      const array = version.split('.');
      return array[0] + '.' + array[1];
    },
  }
}
</script>
