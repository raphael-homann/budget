<script lang="ts">
import Budget from "../Data/Entity/Budget";
import Category from "../Data/Entity/Category";
import BgCategoryEditForm from "../Data/Form/BgCategoryEditForm.vue";
import DetectionMask from "../Data/Entity/DetectionMask";
import BgDetectionMaskEditForm from "../Data/Form/BgDetectionMaskEditForm.vue";
import EntityManager from "../../custom/npm-src/SynergyTS-npm/Data/EntityManager";

export default {
  name: "bg-detection-mask-list",
  components: {BgDetectionMaskEditForm, BgCategoryEditForm},
  props: {
    budget: {
      type: Budget,
      required: true
    },
    entityManager: {
      type: EntityManager,
      required: true
    }
  },
  data():{
    detectionMaskModal: DetectionMask|null
  } {
    return {
      detectionMaskModal:null
    }
  },
  computed: {
    categoriesWithDetectionMask(): Array<Category> {
      // return this.budget.categories;
      console.log('categoriesWithDetectionMask',this.budget.categories)
      return this.budget.categories.filter(category => category.hasDetectionMasks());
    }
  },
  methods: {
    newMask() {
      console.log('newMask !!');
      this.detectionMaskModal = new DetectionMask()
    }
  }

}
</script>

<template>
  <v-dialog v-model="detectionMaskModal" width="auto" :close-on-back="false">
    <bg-detection-mask-edit-form v-if="detectionMaskModal"
        :detection-mask="detectionMaskModal"
        :entity-manager="entityManager"
        @close="detectionMaskModal=null"
    />
  </v-dialog>

  <v-card
      density="compact"
      flat color="transparent"
      class="mx-auto"
      max-width="400"
      title="Detection Masks"
  >
  <v-btn variant="flat" color="secondary" prepend-icon="mdi-plus" @click="newMask">Ajouter</v-btn>
    <v-list>
      <v-list-item v-for="category in categoriesWithDetectionMask" :title="category.name">
        <v-list density="compact" variant="tonal">
          <v-list-item v-for="detectionMask in category.detectionMasks">
<!--            <v-text-field v-model="detectionMask.mask"></v-text-field>-->
            <v-btn size=20 variant="plain" icon="mdi-pencil" @click="detectionMaskModal = detectionMask.clone()"></v-btn>
            {{detectionMask.mask}}
          </v-list-item>

        </v-list>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<style scoped>

</style>
