<template>
  <async-container :loading="!loaded">
    <v-data-table
      :items="items"
      :headers="headers"
      :search="search"
      class="elevation-2"
      no-data-text="Nenhum produto cadastrado"
      no-results-text="Nenhum produto encontrado"
    >
      <template v-slot:top>
        <v-card-title>
          Produtos
          <v-spacer/>
          <v-btn small color="success">Adicionar produto</v-btn>
        </v-card-title>
        <v-card-text>
          <v-text-field
            solo-inverted
            label="Pesquisar"
            prepend-inner-icon="mdi-magnify"
            v-model="search"
          ></v-text-field>
        </v-card-text>
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
      search : '',
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