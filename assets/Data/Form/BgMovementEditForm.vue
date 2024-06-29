<template>
  <v-dialog v-model="categoryModal" width="auto" :close-on-back="false">
    <bg-category-edit-form
        :category="categoryModal"
        :entity-manager="props.entityManager"
        @close="categoryModal=null"
        @save="onCategorySaved"
    />
  </v-dialog>

  <v-card v-if="movement" :title="modalTitle(movement)">
    <v-card-text>

      <!--amount-->
      <v-text-field v-model.number="movement.amount"
                    :label="t('budget.entities.Movement.fields.amount')" required>
      </v-text-field>

      <!--comment-->
      <v-text-field v-model.number="movement.comment"
                    :label="t('budget.entities.Movement.fields.comment')" required>
      </v-text-field>

      <!--date-->
      <bg-date-field :date="movement.date"
                    :label="t('budget.entities.Movement.fields.date')" required>
      </bg-date-field>

      <!-- category -->
      <v-row justify="end" v-if="categoryRepository">
        <v-col cols="8">
          <v-autocomplete
              :items="categoryRepository.getItems()"
              item-title="name"
              item-value="id"
              v-model="movement.categoryId"
              :label="t('budget.entities.Movement.fields.category')"
              required
          ></v-autocomplete>
        </v-col>
        <v-col cols="4">
          <v-btn icon='mdi-plus-thick' @click="categoryModal=new Category()" variant="tonal" color="primary"
                 size="small"></v-btn>
          <v-btn v-if="movement.category" icon='mdi-pencil' @click="editCategory" variant="tonal"
                 color="secondary" size="small"></v-btn>
        </v-col>
      </v-row>
    </v-card-text>

    <v-card-actions>
      <v-container>
        <v-row justify="end">
          <v-btn prepend-icon="mdi-close" color="grey" @click="close()">Fermer</v-btn>
          <v-btn prepend-icon="mdi-check" color="primary" @click="save()">Enregistrer</v-btn>
        </v-row>
      </v-container>
    </v-card-actions>
  </v-card>
</template>
<script setup lang="ts">
import Movement from "../Entity/Movement";
import {useLocale} from "vuetify";
import Entity from "@efrogg/synergy/Data/Entity";
// import SpDateField from "../Form/SpDateField.vue";
import {onMounted, ref, Ref} from "vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import Repository from "@efrogg/synergy/Data/Repository";

// category imports
import Category from "../Entity/Category";
import BgCategoryEditForm from "./BgCategoryEditForm.vue";

const categoryModal: Ref<Category | null> = ref(null);
let categoryRepository: Repository<Category> | null = null;

const props = defineProps({
  entityManager: {
    type: EntityManager,
    required: true
  },
  movement: {
    type: Movement,
    required: true
  }
})
const {t} = useLocale()
const emit = defineEmits(['close', 'save']);

function close() {
  emit('close')
}

onMounted(() => {
  categoryRepository = props.entityManager.getRepository(Category)
})

function modalTitle(entity: Entity): string {
  let className = entity.constructor.name;
  return entity.getId()
      ? t('budget.entities.' + className + '.listing.edit')
      : t('budget.entities.' + className + '.listing.add');
}

function save() {
  props.entityManager
      .save(props.movement)
      .then(() => {
        emit('save', props.movement);
      })
      .catch((e: any) => {
        console.error(e);
      });
}

function editCategory() {
  categoryModal.value = categoryRepository?.get(props.movement.categoryId)?.clone() ?? null
}

function onCategorySaved(category: Category) {
  props.movement.categoryId = category.getId();
  categoryModal.value = null;
}

</script>
