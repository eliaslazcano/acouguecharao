<template>
  <async-container :loading="!loaded">
    <v-data-table
      class="elevation-2"
      no-data-text="Nenhuma venda"
      no-results-text="Nenhuma venda encontrada"
      :items="items"
      :headers="headers"
      :footer-props="{'items-per-page-options': [10, 25, 50, 100]}"
    >
      <template v-slot:top>
        <v-card-title>
          Vendas
          <v-spacer/>
          <v-btn color="success" small>
            <v-icon class="mr-1">mdi-plus</v-icon>
            Nova venda
          </v-btn>
        </v-card-title>
      </template>
    </v-data-table>
  </async-container>
</template>

<script>
import AsyncContainer from '../../components/interface/AsyncContainer';
import VendaWebClient from "../../http/VendaWebClient";
export default {
  name: 'Vendas',
  components: {AsyncContainer},
  data: () => ({
    loaded: false,
    items: [],
    headers: [],
  }),
  methods: {
    async loadData() {
      try {
        const vendaWebClient = new VendaWebClient();
        this.items = await vendaWebClient.getVendas();
      } finally {
        this.loaded = true;
      }
    },
  },
  created() {
    this.loadData();
  }
}
</script>

<style scoped>

</style>