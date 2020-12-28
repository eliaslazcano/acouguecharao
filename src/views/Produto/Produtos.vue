<template>
  <async-container :loading="!loaded">
    <v-data-table
      :items="items"
      :headers="headers"
      class="elevation-2"
    >
      <template v-slot:top>
        <v-card-title>
          Produtos
          <v-spacer/>
          <v-btn small color="success">Adicionar produto</v-btn>
        </v-card-title>
      </template>
      <template v-slot:item.preco="{item}">R$ {{item.preco.toFixed(2)}}</template>
      <template v-slot:item.action>
        <div class="d-flex flex-nowrap">
          <v-btn small icon color="warning">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn small icon color="error">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </div>
      </template>
    </v-data-table>
  </async-container>
</template>

<script>
  import AsyncContainer from '../../components/interface/AsyncContainer';
  import VendaWebClient from '../../http/VendaWebClient';
  export default {
    name: 'Produtos',
    components: {AsyncContainer},
    data: () => ({
      loaded: false,
      items: [],
      headers: [
        {value: 'nome', text: 'Nome'},
        {value: 'preco', text: 'Preço'},
        {value: 'action', text: 'Ações'},
      ],
    }),
    methods: {
      async loadData() {
        const vendaWebClient = new VendaWebClient();
        try {
          this.items = await vendaWebClient.getProdutos();
        } finally {
          this.loaded = true;
        }
      },
    },
    created() {
      this.loadData();
    },
  }
</script>

<style scoped>

</style>