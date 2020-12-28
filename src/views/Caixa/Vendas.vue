<template>
  <async-container :loading="!loaded" fluid>
    <v-data-table
      class="elevation-2"
      no-data-text="Nenhuma venda"
      no-results-text="Nenhuma venda encontrada"
      :items="items"
      :headers="headers"
      :footer-props="{'items-per-page-options': [10, 25, 50, 100]}"
      sort-by="datahora"
      sort-desc
    >
      <template v-slot:top>
        <v-card-title>
          Vendas
          <v-spacer/>
          <v-btn color="success" small to="/caixa/venda">
            <v-icon class="mr-1">mdi-plus</v-icon>
            Nova venda
          </v-btn>
        </v-card-title>
      </template>
      <template v-slot:item.datahora="{item}">{{formatarData(item.datahora)}}</template>
      <template v-slot:item.total="{item}">R$ {{item.total.toFixed(2)}}</template>
      <template v-slot:item.action="{item}">
        <v-btn icon color="primary" :to="'/caixa/venda/' + item.id">
          <v-icon>mdi-eye</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </async-container>
</template>

<script>
import AsyncContainer from '../../components/interface/AsyncContainer';
import VendaWebClient from "../../http/VendaWebClient";
import moment from 'moment';
export default {
  name: 'Vendas',
  components: {AsyncContainer},
  data: () => ({
    loaded: false,
    items: [],
    headers: [
      {value: 'datahora', text: 'Data'},
      {value: 'total', text: 'Valor'},
      {value: 'action', text: 'Detalhes'},
    ],
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
    formatarData(datetime) {
      return moment(datetime).format('DD/MM/YYYY HH:mm');
    },
  },
  created() {
    this.loadData();
  }
}
</script>

<style scoped>

</style>