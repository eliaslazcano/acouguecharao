<template>
  <async-container :loading="!loaded">
    <!-- Tabela de produtos -->
    <v-data-table
      :items="items"
      :headers="headers"
      :search="search"
      class="elevation-2"
      no-data-text="Nenhum produto cadastrado"
      no-results-text="Nenhum produto encontrado"
      sort-by="nome"
    >
      <template v-slot:top>
        <v-card-title>
          Produtos
          <v-spacer/>
          <v-btn small color="success" @click="adicionarProduto">Adicionar produto</v-btn>
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
      <template v-slot:item.action="{item}">
        <div class="d-flex flex-nowrap">
          <v-btn small icon color="warning" @click="editarProduto(item.id, item.nome, item.codigo, item.preco)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn small icon color="error" @click="deleteProduto(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </div>
      </template>
    </v-data-table>
    <!-- Dialog: Add ou editar produto -->
    <v-dialog width="400" max-width="94%" v-model="dialogEditor">
      <v-card>
        <v-card-title>{{iptId ? 'Editar produto' : 'Registrar produto'}}</v-card-title>
        <v-card-text>
          <v-text-field
            label="Nome do produto"
            v-model="iptNome"
            :disabled="loadingEditor"
          ></v-text-field>
          <v-text-field
            label="Código"
            v-model="iptCodigo"
            hint="Número do código de barras"
            :disabled="loadingEditor"
          ></v-text-field>
          <vuetify-money
            label="Preço"
            v-model="iptPreco"
            :disabled="loadingEditor"
          ></vuetify-money>
        </v-card-text>
        <v-card-actions class="justify-center">
          <v-btn color="primary" @click="updateProduto" :loading="loadingEditor">Salvar</v-btn>
          <v-btn color="warning" @click="dialogEditor = false" :disabled="loadingEditor">Cancelar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Dialog: Apagar produto -->
    <v-dialog width="400" max-width="94%" v-model="dialogDelete" persistent>
      <v-card>
        <v-card-title style="background-color: #f5f5f5; border-bottom: 1px solid #dbdbdb">Excluir produto</v-card-title>
        <v-card-text class="mt-3">
          <div class="d-flex align-center">
            <v-icon size="48" class="mr-3" color="error">mdi-alert</v-icon>
            <p class="body-1 flex-grow-1 mb-0">Remover <span class="font-weight-bold">"{{iptNome}}"</span>?</p>
          </div>
        </v-card-text>
        <v-card-actions style="background-color: #f5f5f5; border-top: 1px solid #dbdbdb">
          <v-spacer></v-spacer>
          <v-btn color="secondary" @click="dialogDelete = false" :disabled="deleting">CANCELAR</v-btn>
          <v-btn color="error" @click="deleteProduto(null)" :loading="deleting">CONFIRMAR</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
        {value: 'codigo', text: 'Codigo'},
        {value: 'action', text: 'Ações'},
      ],
      search : '',
      dialogEditor: false,
      dialogDelete: false,
      iptNome: '',
      iptCodigo: '',
      iptPreco: '',
      iptId: null,
      loadingEditor: false,
      deleting: false,
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
      limparCampos() {
        this.iptNome = '';
        this.iptCodigo = '';
        this.iptPreco = '';
        this.iptId = null;
      },
      adicionarProduto() {
        this.limparCampos();
        this.dialogEditor = true;
      },
      editarProduto(id, nome, codigo, preco) {
        this.limparCampos();
        this.iptId = id;
        this.iptNome = nome;
        this.iptCodigo = codigo;
        this.iptPreco = preco.toString();
        this.dialogEditor = true;
      },
      async updateProduto() {
        const vendaWebClient = new VendaWebClient();
        this.loadingEditor = true;
        try {
          if (this.iptId) await vendaWebClient.updateProduto(this.iptId, this.iptNome, this.iptCodigo, parseFloat(this.iptPreco));
          else await vendaWebClient.insertProduto(this.iptNome, this.iptCodigo, parseFloat(this.iptPreco));
          await this.loadData();
          this.dialogEditor = false;
        } finally {
          this.loadingEditor = false;
        }
      },
      async deleteProduto(produto = null) { //null = envia para o banco o pedido, senão, apenas abre o modal
        if (produto) {
          this.iptId = produto.id;
          this.iptNome = produto.nome;
          this.dialogDelete = true;
        } else if (this.iptId) {
          const vendaWebClient = new VendaWebClient();
          this.deleting = true;
          try {
            await vendaWebClient.deleteProduto(this.iptId);
            await this.loadData();
            this.dialogDelete = false;
            this.limparCampos();
          } finally {
            this.deleting = false;
          }
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