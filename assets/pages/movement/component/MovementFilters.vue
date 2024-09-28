<script lang="ts">
import EntityManager from '@efrogg/synergy/Data/EntityManager';
import Category from "../../../Data/Entity/Category";
import Envelope from "../../../Data/Entity/Envelope";

export type ListFilters = {
  categoryId?: Array<number>,
  envelopeId?: Array<number>,
  label?: string,
};

export default {
  name: "bg-movement-filters",
  props: {
    filters: {
      type: Object as () => ListFilters,
      required: true
    },
    entityManager: {
      type: EntityManager,
      required: true
    }
  },
  emits: ['update:filters'],
  data(): {
  } {
    return {
    }
  },
  mounted() {
  },
  computed: {
    categories(): Category[] {
      let newCategory = new Category();
      newCategory.name = 'sans catégorie'
      return [
          newCategory,
          ...this.entityManager.getRepository(Category).getItems()
      ]
    },
    envelopes(): Envelope[] {
      let newEnvelope = new Envelope();
      newEnvelope.name = 'sans enveloppe';
      return [
          newEnvelope,
          ...this.entityManager.getRepository(Envelope).getItems()
      ]
    }
  },
  methods: {
    emitUpdate() {
      this.$emit('update:filters', this.filters)
    }
  }
}
</script>

<template>
  <v-row>
    <v-col cols="12" md="4">
      <v-text-field v-model="filters.label" @update:model-value="emitUpdate" label="Nom" outlined></v-text-field>
    </v-col>
<!--    <v-col cols="12" md="6">-->
<!--      <v-text-field v-model="filters.amount" label="Montant" outlined></v-text-field>-->
<!--    </v-col>-->
<!--    <v-col cols="12" md="6">-->
<!--      <v-text-field v-model="filters.date" label="Date" outlined></v-text-field>-->
<!--    </v-col>-->
    <v-col cols="12" md="4">
      <v-autocomplete v-model="filters.categoryId" multiple @update:model-value="emitUpdate" :items="categories" item-value="id" item-title="name" label="Catégorie" outlined>
      </v-autocomplete>
    </v-col>
    <v-col cols="12" md="4">
      <v-autocomplete v-model="filters.envelopeId" multiple @update:model-value="emitUpdate" :items="envelopes" item-value="id" item-title="name" label="Enveloppe" outlined>
      </v-autocomplete>
    </v-col>
  </v-row>
</template>

<style scoped>

</style>
