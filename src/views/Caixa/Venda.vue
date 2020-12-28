<template>
  <async-container :loading="!loaded" fluid>
    <!-- Inserção de produto -->
    <v-card v-if="!id">
      <v-card-text>
        <v-text-field
          label="Código do produto"
          placeholder="Escreva o código para adicionar um produto"
          v-model="iptCodigoProduto"
          outlined
          hide-details
          dense
          append-outer-icon="mdi-send"
          @click:append-outer="inserirProduto"
          @keydown.enter="inserirProduto"
          append-icon="mdi-magnify"
          @click:append="dialogProdutos = true"
        ></v-text-field>
      </v-card-text>
    </v-card>
    <!-- Produtos -->
    <v-data-table
      :headers="headers"
      :items="items"
      class="elevation-2 my-5"
      :items-per-page="-1"
      hide-default-footer
      no-data-text="Nenhum produto adicionado"
    >
      <template v-slot:top>
        <v-card-title v-if="!!id">Produtos vendidos</v-card-title>
      </template>
      <template v-slot:item.produto="{item}">{{getProdutoNome(item.produto)}}</template>
      <template v-slot:item.preco="{item}">{{formatarValor(item.preco)}}</template>
      <template v-slot:item.quantidade="{item}">
        <v-text-field
          dense
          outlined
          hide-details
          single-line
          v-model="item.quantidade"
          @keydown.prevent="quantidadePress($event, item.produto)"
          :disabled="!!id"
        ></v-text-field>
      </template>
      <template v-slot:item.total="{item}">
        <span :class="{'red--text': parseFloat(item.quantidade) === 0}">{{formatarValor(parseFloat(item.quantidade) * item.preco)}}</span>
      </template>
      <template v-slot:item.actions="{item}">
        <v-btn icon color="error" @click="removerProduto(item.produto)" :disabled="!!id">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>
    <!-- Total -->
    <div class="d-flex justify-center">
      <v-card elevation="2" outlined>
        <v-card-text>
          <p class="text-h5 mb-0 text-center">TOTAL</p>
          <p class="text-h2 mb-0 text-right">R$ {{valorTotal}}</p>
          <div v-if="!!id" class="text-right">
            <v-divider class="my-1"/>
            <p class="subtitle-1 mb-0">Desconto: R$ {{parseFloat(iptDesconto).toFixed(2)}}</p>
            <p class="subtitle-1 mb-0">Valor cobrado: R$ {{(parseFloat(valorTotal) - parseFloat(iptDesconto)).toFixed(2)}}</p>
          </div>
        </v-card-text>
        <v-divider/>
        <v-card-actions class="justify-center" v-if="!id">
          <v-btn rounded color="error" @click="cobrar">
            <v-icon class="mr-2">mdi-cash-check</v-icon>
            COBRAR
          </v-btn>
        </v-card-actions>
      </v-card>
    </div>
    <!-- Dialog: Busca produto -->
    <v-dialog width="780" max-width="94%" v-model="dialogProdutos">
      <v-card>
        <v-card-title>
          Buscar produto
          <v-spacer/>
          <v-icon @click="dialogProdutos = false">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-text-field
            v-model="searchProduto"
            outlined
            dense
            placeholder="Digite para buscar"
          ></v-text-field>
          <v-data-table
            :headers="[
              {value: 'nome', text: 'nome'},
              {value: 'codigo', text: 'codigo'},
              {value: 'preco', text: 'preço'},
            ]"
            :items="produtos"
            :search="searchProduto"
            dense
            @click:row="inserirProdutoPelaBusca"
            class="itemhover"
            :items-per-page="10"
          >
            <template v-slot:item.preco="{item}">{{formatarValor(item.preco)}}</template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- Dialog: Cobrar -->
    <v-dialog width="500" max-width="94%" v-model="dialogCobrar">
      <v-card>
        <v-card-title>
          Pagamento da venda
        </v-card-title>
        <v-card-text class="mt-3">
          <v-text-field
            label="Total dos produtos"
            :value="valorTotal"
            outlined
            prefix="R$"
            readonly
            dense
          ></v-text-field>
          <v-text-field
            label="Desconto"
            placeholder="Digite um desconto se houver"
            v-model="iptDesconto"
            outlined
            prefix="R$"
            @keydown.prevent="descontoPress"
            dense
          ></v-text-field>
          <v-divider/>
          <div class="text-center mt-2">
            <p class="subtitle-1 mb-0">Total a cobrar</p>
            <p class="title mb-0">R$ {{(parseFloat(valorTotal) - parseFloat(iptDesconto)).toFixed(2)}}</p>
          </div>
        </v-card-text>
        <v-divider/>
        <v-card-actions class="justify-center">
          <v-btn color="success" rounded :loading="registrandoVenda" @click="confirmarPagamento">Confirmar pagamento</v-btn>
          <v-btn color="error" rounded @click="dialogCobrar = false" :disabled="registrandoVenda">Cancelar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </async-container>
</template>

<script>
  import AsyncContainer from '../../components/interface/AsyncContainer';
  import VendaWebClient from '../../http/VendaWebClient';
  export default {
    name: 'Venda',
    props: ['id'],
    components: {AsyncContainer},
    data: () => ({
      loaded: false,
      produtos: [],
      headers: [
        {value: 'produto', text: 'Produto'},
        {value: 'preco', text: 'Preço Un|Kg'},
        {value: 'quantidade', text: 'Quantidade', width: '7rem'},
        {value: 'total', text: 'Total'},
        {value: 'actions', text: ''},
      ],
      items: [], // {produto: Number, quantidade: Number, preco: Number}
      iptCodigoProduto: '',
      dialogProdutos: false,
      dialogCobrar: false,
      searchProduto: '',
      iptDesconto: '0',
      registrandoVenda: false,
    }),
    methods: {
      async loadData() {
        const vendaWebClient = new VendaWebClient();
        try {
          this.produtos = await vendaWebClient.getProdutos();
          if (this.id) {
            const venda = await vendaWebClient.getVenda(this.id);
            this.iptDesconto = venda.desconto.toString();
            this.items = venda.produtos;
          }
        } finally {
          this.loaded = true;
        }
      },
      inserirProduto() {
        const produto = this.produtos.find(p => p.codigo === this.iptCodigoProduto.trim());
        if (!produto) {
          this.$snackbar({text: 'Nenhum produto corresponde ao código', color: 'warning'});
          return;
        }
        const indice = this.items.findIndex(p => p.produto === produto.id);
        if (indice === -1) {
          //O produto ainda não está na lista
          this.items.push({
            produto: produto.id,
            quantidade: '1',
            preco: produto.preco,
          });
        } else {
          this.items[indice].quantidade = (parseFloat(this.items[indice].quantidade) + 1).toString();
        }
        this.iptCodigoProduto = '';
      },
      formatarValor(double) {
        return 'R$ ' + double.toFixed(2);
      },
      getProdutoNome(produtoId) {
        return this.produtos.find(p => p.id === produtoId).nome;
      },
      quantidadePress(event, produto) {
        const index = this.items.findIndex(i => i.produto === produto);
        if (/^\d+$/.test(event.key)) {
          this.items[index].quantidade = parseFloat(this.items[index].quantidade + event.key).toString();
        } else if (event.key === ',' || event.key === '.') {
          if (this.items[index].quantidade.indexOf('.') === -1) this.items[index].quantidade += '.';
        } else if (event.key === 'Backspace') {
          if (this.items[index].quantidade.length === 1) this.items[index].quantidade = '0';
          else if (this.items[index].quantidade.length > 0) this.items[index].quantidade = this.items[index].quantidade.substr(0, this.items[index].quantidade.length - 1);
        }
      },
      removerProduto(id) {
        const index = this.items.findIndex(i => i.produto === id);
        this.items.splice(index, 1);
      },
      inserirProdutoPelaBusca(produto) {
        this.iptCodigoProduto = produto.codigo;
        this.inserirProduto();
        this.dialogProdutos = false;
        this.$snackbar({text: 'Produto inserido', color: 'success', timeout: 1500});
      },
      cobrar() {
        const itemSemQuantidade = this.items.find(i => parseFloat(i.quantidade) === 0);
        if (itemSemQuantidade) {
          this.$snackbar({text: 'Existe produto sem quantidade', color: 'warning', timeout: 4000});
          return;
        }
        const total = this.items.reduce((carry, item) => carry + (parseFloat(item.quantidade) * item.preco), 0);
        if (total === 0) {
          this.$snackbar({text: 'O valor da conta está zerado', color: 'warning', timeout: 4000});
          return;
        }
        this.dialogCobrar = true;
      },
      descontoPress(event) {
        if (/^\d+$/.test(event.key)) {
          this.iptDesconto = parseFloat(this.iptDesconto + event.key).toString();
        } else if (event.key === ',' || event.key === '.') {
          if (this.iptDesconto.indexOf('.') === -1) this.iptDesconto += '.';
        } else if (event.key === 'Backspace') {
          if (this.iptDesconto.length === 1) this.iptDesconto = '0';
          else if (this.iptDesconto.length > 0) this.iptDesconto = this.iptDesconto.substr(0, this.iptDesconto.length - 1);
        }
      },
      async confirmarPagamento() {
        const vendaWebClient = new VendaWebClient();
        try {
          this.registrandoVenda = true;
          const produtos = this.items.map(i => {
            i.quantidade = parseFloat(i.quantidade);
            return i;
          });
          await vendaWebClient.insertVenda(produtos, parseFloat(this.iptDesconto));
          this.$snackbar({text: 'Venda registrada', color: 'success'});
          this.$router.push('/caixa/vendas');
        } catch (e) {
          console.log(e);
          this.$snackbar({text: 'Ocorreu um erro', color: 'error', timeout: 3000});
        } finally {
          this.registrandoVenda = false;
        }
      },
    },
    created() {
      this.loadData();
    },
    computed: {
      valorTotal() {
        return this.items.reduce((carry, item) => carry + (parseFloat(item.quantidade) * item.preco), 0).toFixed(2);
      },
    },
  }
</script>

<style>
  .itemhover tr {
    cursor: pointer;
  }
</style>