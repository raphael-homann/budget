<script lang="ts">
import Budget from "../Data/Entity/Budget";
import BgBudgetEditForm from "../Data/Form/BgBudgetEditForm.vue";


export default {
  name: "budget-card",
  components: {BgBudgetEditForm},
  props: {
    budget: {
      type: Budget,
      required: true
    }
  },
  emits: ['edit', 'delete']

}
</script>

<template>
  <v-card
      class="mx-auto"
      max-width="400"
  >
    <router-link :to="{name: 'budget-view', params: {id: budget.id}}">

    <v-img
        class="align-end text-white"
        height="200"
        src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
        cover
    >
      <v-card-title>{{ budget.name }}</v-card-title>
    </v-img>
    </router-link>

    <v-card-subtitle class="pt-4">
      {{ budget.description }}
    </v-card-subtitle>

    <!--    <v-card-text>-->
    <!--      <div>Whitehaven Beach</div>-->

    <!--      <div>Whitsunday Island, Whitsunday Islands</div>-->
    <!--    </v-card-text>-->

    <v-card-actions>
      <v-btn color="orange" text="edit" @click="console.log('edit',budget);$emit('edit', budget)"></v-btn>
      <v-btn color="orange" text="view" :to="{name: 'budget-view', params: {id: budget.id}}"></v-btn>
      <v-btn color="red" text="delete" @click="$emit('delete', budget)"></v-btn>
    </v-card-actions>
  </v-card>

</template>

<style scoped>

</style>
