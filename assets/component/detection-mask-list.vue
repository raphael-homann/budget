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
    },
    detectionMaskInput: {
      type: DetectionMask,
      default: null
    }
  },
  data(): {
    detectionMaskModal: DetectionMask | null,
    onlyUncategorized: boolean,
    filter: string
  } {
    return {
      detectionMaskModal: null,
      onlyUncategorized: true,
      filter: '',
    }
  },
  computed: {
    categoriesWithDetectionMask(): Array<Category> {
      // return this.budget.categories;
      console.log('categoriesWithDetectionMask', this.budget.categories)
      return this.budget.categories.filter(category => this.filterDetectionMasks(category).length > 0);
    }
  },
  watch: {
    detectionMaskInput: {
      handler: function (value: DetectionMask) {
        this.detectionMaskModal = value;
      },
      immediate: true
    }
  },
  methods: {
    // permet d'échanger le mask de la modal avec le mask du repository
    // reloadDetectionMask(detectionMask: DetectionMask): DetectionMask {
    //   return this.entityManager.getRepository(DetectionMask).get(detectionMask.id)?.clone() ?? detectionMask;
    // },
    filterDetectionMasks(category: Category): Array<DetectionMask> {
      return category.detectionMasks.filter(detectionMask => detectionMask.mask.toLowerCase().includes(this.filter.toLowerCase()));
    },
    newMask() {
      this.detectionMaskModal = new DetectionMask();
      this.detectionMaskModal.score = 100;
    },
    executeAll() {
      this.executeDetection()
    },
    executeDetection(detectionMask?: DetectionMask) {
      let body: {
        onlyUncategorized: boolean,
        detectionMaskId?: number
        budgetId?: number
      } = {
        onlyUncategorized: this.onlyUncategorized
      }
      let endpoint = this.buildDetectionEndpoint(detectionMask);
      // if(detectionMask) {
      //   body.detectionMaskId = detectionMask.id
      // } else {
      //   body.budgetId = this.budget.id
      // }


      let fetchParams: { method: string, body: string | null } = {
        method: 'POST',
        body: JSON.stringify(body)
      }

      fetch(endpoint, fetchParams).then(response => {
        console.log(response);
      }).catch(error => {
        console.error(error);
      });

      console.log('execute mask ', detectionMask?.mask);
    },
    buildDetectionEndpoint(detectionMask?: DetectionMask): string {
      let endpoint = '/budget/' + this.budget.id + '/executeDetection';
      if (detectionMask) {
        endpoint += '/' + detectionMask.id;
      }
      return endpoint;

    }
  }

}
</script>

<template>
  <v-dialog v-model="detectionMaskModal" width="auto" min-width="500" :close-on-back="false">
    <bg-detection-mask-edit-form v-if="detectionMaskModal"
                                 :detection-mask="detectionMaskModal"
                                 :entity-manager="entityManager"
                                 @close="detectionMaskModal=null"
                                 @play="executeDetection(detectionMaskModal)"
    />
<!--                                 @save="detectionMaskModal=reloadDetectionMask(detectionMaskModal)"-->
  </v-dialog>

  <v-card
      density="compact"
      flat color="transparent"
      class="mx-auto"
      max-width="400"
      title="Detection Masks"
  >
    <v-list-item>
      <v-checkbox label="only unused" v-model="onlyUncategorized"></v-checkbox>
      <v-btn variant="flat" color="warning" prepend-icon="mdi-play" text="Execute All" @click="executeAll()"></v-btn>
    </v-list-item>
    <v-divider/>


    <!--    Add-->
    <v-list-item>
      <v-btn variant="flat" color="secondary" prepend-icon="mdi-plus" @click="newMask">Ajouter</v-btn>
    </v-list-item>
    <v-divider/>
      <v-text-field v-model="filter" label="Filtre" outlined></v-text-field>

    <!--    Liste-->
    <v-list>
      <v-list-item v-for="category in categoriesWithDetectionMask" :title="category.name">
        <v-list density="compact" variant="tonal">
          <v-list-item v-for="detectionMask in filterDetectionMasks(category)">
            <v-btn size=20 variant="plain" icon="mdi-play" @click="executeDetection(detectionMask)"></v-btn>
            <v-btn size=20 variant="plain" icon="mdi-pencil"
                   @click="detectionMaskModal = detectionMask.clone()"></v-btn>
            {{ detectionMask.mask }}
          </v-list-item>

        </v-list>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<style scoped>

</style>
