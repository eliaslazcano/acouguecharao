<template>
  <v-container>
    <p class="title mb-0">Caixa</p>
    <p class="subtitle-1">Operações do caixa da empresa</p>
    <v-divider class="mb-5"/>
    <v-sheet v-for="i in items" :key="i.to" class="mb-3" outlined light rounded>
      <v-list-item :to="i.to">
        <v-list-item-icon>
          <v-icon>mdi-{{i.icon}}</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{i.title}}</v-list-item-title>
          <v-list-item-subtitle>{{i.subtitle}}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-sheet>
  </v-container>
</template>

<script>
export default {
  name: 'Index',
  data: () => ({
    items: [
      {to: '/caixa/vender', icon: 'account-group', title: 'Comissões de Trabalho', subtitle: ''},
      {to: '/diretoria/conselheiros', icon: 'account-star', title: 'Conselheiros', subtitle: ''},
      {to: '/diretoria/delegados', icon: 'shield-account', title: 'Delegados', subtitle: ''},
      {to: '/diretoria/diretores', icon: 'account-tie', title: 'Diretores', subtitle: ''},
    ],
  }),
}
</script>

<style scoped>

</style>