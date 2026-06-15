<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    records: Object, // Inapokea Pagination Object kwa usahihi sasa hivi
    sires: Array,
    dams: Array,
    stats: Object,
    isAdmin: Boolean
});

// State Management
const showAlert = ref(false);
const searchQuery = ref('');
const activePenFilter = ref('Wote');
const activeCategoryFilter = ref('Wote'); 
const activeFormTab = ref('pig');
const dynamicWeightId = ref(null);

// Hali ya Kuhariri (Edit Mode Tracking)
const isEditing = ref(false);
const editingRecordId = ref(null);

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);

watch(flashMessage, (newMessage) => {
    if (newMessage) {
        showAlert.value = true;
        setTimeout(() => { showAlert.value = false; }, 4000);
    }
}, { immediate: true });

// FOMU YA 1: Nguruwe Mkuu
const pigForm = useForm({
    record_type: 'pig',
    pig_code: '',
    title: '',
    gender: 'Jike',
    age_manual: '', 
    breed: 'Large White', 
    castration_status: 'Hajahasiwa',
    birth_date: '',
    pen_number: 'Banda A',
    status: 'Anakuwa',
    sire_code: '',
    dam_code: ''
});

// FOMU YA 2: Kundi la Watoto
const litterForm = useForm({
    record_type: 'litter',
    pig_code: '',
    gender: 'Changanyiko', 
    age_manual: '', 
    breed: 'Chotara', 
    litter_size: 1,
    birth_date: '',
    weaning_date: '', 
    status: 'Mtoto',
    pen_number: 'Banda la Uzazi',
    sire_code: '',
    dam_code: ''
});

const weightForm = useForm({ new_weight: '' });

const calculateAge = (birthDate) => {
    if (!birthDate) return '-';
    const birth = new Date(birthDate);
    const today = new Date();
    let months = (today.getFullYear() - birth.getFullYear()) * 12 + (today.getMonth() - birth.getMonth());
    if (months < 1) {
        const days = Math.floor((today - birth) / (1000 * 60 * 60 * 24));
        return `${days} Siku`;
    }
    if (months >= 12) {
        const years = Math.floor(months / 12);
        const remMonths = months % 12;
        return `${years} Mwaka ${remMonths} Miezi`;
    }
    return `${months} Miezi`;
};

// Kuchuja data (Inasoma kutoka kwenye props.records.data)
const filteredPigs = computed(() => {
    const recordList = props.records?.data || [];
    return recordList.filter(pig => {
        const matchesSearch = pig.pig_code?.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                              pig.breed?.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesPen = activePenFilter.value === 'Wote' || pig.pen_number === activePenFilter.value;
        
        let matchesCategory = true;
        if (activeCategoryFilter.value === 'Boars') matchesCategory = pig.gender === 'Dume' && pig.record_type !== 'litter';
        if (activeCategoryFilter.value === 'Sows') matchesCategory = pig.gender === 'Jike' && pig.record_type !== 'litter';
        if (activeCategoryFilter.value === 'Weaners') matchesCategory = pig.status === 'Mtoto' || pig.record_type === 'litter';
        
        return matchesSearch && matchesPen && matchesCategory;
    });
});

const selectedSireData = computed(() => props.sires.find(s => s.pig_code === litterForm.sire_code));
const selectedDamData = computed(() => props.dams.find(d => d.pig_code === litterForm.dam_code));

watch(() => litterForm.birth_date, (newDate) => {
    if (newDate) {
        const date = new Date(newDate);
        date.setDate(date.getDate() + 35);
        litterForm.weaning_date = date.toISOString().split('T')[0];
    }
});

const uniqueFarmPens = computed(() => {
    const recordList = props.records?.data || [];
    const pens = recordList.map(p => p.pen_number).filter(Boolean);
    return ['Wote', ...new Set(pens)];
});

const editRecord = (pig) => {
    isEditing.value = true;
    editingRecordId.value = pig.id;
    activeFormTab.value = pig.record_type;

    if (pig.record_type === 'pig') {
        pigForm.pig_code = pig.pig_code || '';
        pigForm.title = pig.title || '';
        pigForm.gender = pig.gender || 'Jike';
        pigForm.age_manual = pig.age_manual || '';
        pigForm.breed = pig.breed || 'Large White';
        pigForm.castration_status = pig.castration_status || 'Hajahasiwa';
        pigForm.birth_date = pig.birth_date || '';
        pigForm.pen_number = pig.pen_number || 'Banda A';
        pigForm.status = pig.status || 'Anakuwa';
    } else {
        litterForm.pig_code = pig.pig_code || '';
        litterForm.gender = pig.gender || 'Changanyiko';
        litterForm.age_manual = pig.age_manual || '';
        litterForm.breed = pig.breed || 'Chotara';
        litterForm.litter_size = pig.litter_size || 1;
        litterForm.birth_date = pig.birth_date || '';
        litterForm.weaning_date = pig.weaning_date || '';
        litterForm.status = pig.status || 'Mtoto';
        litterForm.pen_number = pig.pen_number || 'Banda la Uzazi';
        litterForm.sire_code = pig.sire_code || '';
        litterForm.dam_code = pig.dam_code || '';
    }
};

const cancelEdit = () => {
    isEditing.value = false;
    editingRecordId.value = null;
    pigForm.reset();
    litterForm.reset();
};

const deleteRecord = (id) => {
    if (confirm('Je, una uhakika unataka kufuta kabisa rekodi hii ya nguruwe?')) {
        router.delete(route('records.destroy', id), {
            preserveScroll: true,
            onSuccess: () => { if(editingRecordId.value === id) cancelEdit(); }
        });
    }
};

const submitPig = () => {
    if (isEditing.value) {
        pigForm.put(route('records.update', editingRecordId.value), {
            preserveScroll: true,
            onSuccess: () => cancelEdit()
        });
    } else {
        pigForm.post(route('records.store'), { 
            preserveScroll: true, 
            onSuccess: () => {
                pigForm.reset(); 
                router.reload(); 
            }
        });
    }
};

const submitLitter = () => {
    if (selectedSireData.value && selectedDamData.value) {
        litterForm.breed = `${selectedSireData.value.breed} x ${selectedDamData.value.breed}`;
    }

    if (isEditing.value) {
        litterForm.put(route('records.update', editingRecordId.value), {
            preserveScroll: true,
            onSuccess: () => cancelEdit()
        });
    } else {
        litterForm.post(route('records.store'), { 
            preserveScroll: true, 
            onSuccess: () => {
                litterForm.reset();
                router.reload(); 
            }
        });
    }
};
</script>

<template>
    <Head title="Meneja wa Shamba la Nguruwe" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Meneja wa Shamba la Nguruwe</h2>
                <span v-if="isEditing" class="bg-amber-100 text-amber-800 font-bold text-xs px-3 py-1 rounded-full animate-pulse">
                    Njia ya Kuhariri Data (Edit Mode Active)
                </span>
            </div>
        </template>

        <div class="py-6 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div @click="activeCategoryFilter = 'Wote'" :class="activeCategoryFilter === 'Wote' ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-xs text-slate-500 font-bold uppercase">Jumla ya Nguruwe</p>
                        <h3 class="text-2xl font-black text-indigo-600">{{ props.stats?.total_pigs || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Boars'" :class="activeCategoryFilter === 'Boars' ? 'ring-2 ring-blue-500 bg-blue-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-xs text-slate-500 font-bold uppercase">Madume</p>
                        <h3 class="text-2xl font-black text-blue-600">{{ props.stats?.boars || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Sows'" :class="activeCategoryFilter === 'Sows' ? 'ring-2 ring-pink-500 bg-pink-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-xs text-slate-500 font-bold uppercase">Majike</p>
                        <h3 class="text-2xl font-black text-pink-600">{{ props.stats?.sows || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Weaners'" :class="activeCategoryFilter === 'Weaners' ? 'ring-2 ring-amber-500 bg-amber-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-xs text-slate-500 font-bold uppercase">Watoto</p>
                        <h3 class="text-2xl font-black text-amber-600">{{ props.stats?.weaners || 0 }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
                    
                    <div :class="isEditing ? 'ring-2 ring-amber-400 bg-amber-50/20' : 'bg-white'" 
                         class="lg:col-span-1 p-4 rounded-2xl shadow-sm border border-slate-200 lg:sticky lg:top-6 max-h-[calc(100vh-120px)] overflow-y-auto transition-all">
                        
                        <div v-if="!isEditing" class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl mb-4 text-[10px]">
                            <button @click="activeFormTab = 'pig'" :class="activeFormTab === 'pig' ? 'bg-white shadow-sm font-bold' : ''" class="py-2 rounded-lg">NGURUWE MKUU</button>
                            <button @click="activeFormTab = 'litter'" :class="activeFormTab === 'litter' ? 'bg-white shadow-sm font-bold' : ''" class="py-2 rounded-lg">KUNDI LA WATOTO</button>
                        </div>
                        <div v-else class="text-center bg-amber-100 text-amber-900 font-black text-[10px] py-2 rounded-lg mb-4 uppercase tracking-wider">
                            Unahariri: {{ activeFormTab === 'pig' ? 'Nguruwe Mkuu' : 'Kundi la Watoto' }}
                        </div>

                        <form v-if="activeFormTab === 'pig'" @submit.prevent="submitPig" class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">ID YA SIKIO</label>
                                <input v-model="pigForm.pig_code" type="text" class="w-full border-slate-200 rounded-xl text-sm" required />
                                <span v-if="pigForm.errors.pig_code" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.pig_code }}</span>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">JINA / UTAMBULISHO</label>
                                <input v-model="pigForm.title" type="text" class="w-full border-slate-200 rounded-xl text-sm" placeholder="e.g Mwamba" />
                                <span v-if="pigForm.errors.title" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.title }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">JINSIA</label>
                                    <select v-model="pigForm.gender" class="w-full border-slate-200 rounded-xl text-xs">
                                        <option value="Jike">Jike</option>
                                        <option value="Dume">Dume</option>
                                    </select>
                                    <span v-if="pigForm.errors.gender" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.gender }}</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">UMRI (AGE)</label>
                                    <input v-model="pigForm.age_manual" type="text" placeholder="e.g 2 Miezi" class="w-full border-slate-200 rounded-xl text-sm" />
                                    <span v-if="pigForm.errors.age_manual" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.age_manual }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">BREED</label>
                                <select v-model="pigForm.breed" class="w-full border-slate-200 rounded-xl text-xs">
                                    <option value="Large White">Large White</option>
                                    <option value="Landrace">Landrace</option>
                                    <option value="Duroc">Duroc</option>
                                    <option value="Chotara">Chotara</option>
                                </select>
                                <span v-if="pigForm.errors.breed" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.breed }}</span>
                            </div>
                            <div v-if="pigForm.gender === 'Dume'">
                                <label class="block text-[11px] font-bold text-slate-600">HALI YA KUHASIWA</label>
                                <select v-model="pigForm.castration_status" class="w-full border-slate-200 rounded-xl text-xs">
                                    <option value="Hajahasiwa">Hajahasiwa</option>
                                    <option value="Amehasiwa">Amehasiwa</option>
                                </select>
                                <span v-if="pigForm.errors.castration_status" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.castration_status }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">BANDA</label>
                                    <input v-model="pigForm.pen_number" type="text" class="w-full border-slate-200 rounded-xl text-sm" required />
                                    <span v-if="pigForm.errors.pen_number" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.pen_number }}</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">MAKUZI</label>
                                    <select v-model="pigForm.status" class="w-full border-slate-200 rounded-xl text-xs">
                                        <option value="Anakuwa">Anakuwa</option>
                                        <option value="Mzazi">Mzazi / Mtambo</option>
                                        <option value="Mtoto">Mtoto</option>
                                    </select>
                                    <span v-if="pigForm.errors.status" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.status }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">TAREHE YA KUZALIWA</label>
                                <input v-model="pigForm.birth_date" type="date" class="w-full border-slate-200 rounded-xl text-xs" required />
                                <span v-if="pigForm.errors.birth_date" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.birth_date }}</span>
                            </div>
                            <div class="space-y-2 pt-2">
                                <button type="submit" :disabled="pigForm.processing" :class="isEditing ? 'bg-amber-500' : 'bg-indigo-600'" class="w-full py-3 text-white rounded-xl font-bold text-xs uppercase transition">
                                    {{ pigForm.processing ? 'Inahifadhi...' : (isEditing ? 'Huisha Nguruwe' : 'Hifadhi Nguruwe') }}
                                </button>
                                <button v-if="isEditing" type="button" @click="cancelEdit" class="w-full py-2 bg-slate-200 text-slate-700 rounded-xl font-bold text-xs uppercase">Ghairi</button>
                            </div>
                        </form>

                        <form v-if="activeFormTab === 'litter'" @submit.prevent="submitLitter" class="space-y-3">
                            
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">ID YA MTOTO WA NGURUWE</label>
                                <input v-model="litterForm.pig_code" type="text" class="w-full border-slate-200 rounded-xl text-sm" required />
                                <span v-if="litterForm.errors.pig_code" class="text-xs text-red-500 font-bold block mt-1">{{ litterForm.errors.pig_code }}</span>
                            </div>

                           

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">JINSIA</label>
                                    <select v-model="litterForm.gender" class="w-full border-slate-200 rounded-xl text-xs">
                                        <option value="Changanyiko">Changanyiko</option>
                                        <option value="Dume Tupu">Dume Tupu</option>
                                        <option value="Jike Tupu">Jike Tupu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">UMRI (AGE MANUAL)</label>
                                    <input v-model="litterForm.age_manual" type="text" placeholder="e.g 3 Wiki" class="w-full border-slate-200 rounded-xl text-sm" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">BREED (AINA YA UKOO)</label>
                                <select v-model="litterForm.breed" class="w-full border-slate-200 rounded-xl text-xs">
                                    <option value="Chotara">Chotara</option>
                                    <option value="Large White">Large White</option>
                                    <option value="Landrace">Landrace</option>
                                    <option value="Duroc">Duroc</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">IDADI YA WATOTO</label>
                                    <input v-model="litterForm.litter_size" type="number" class="w-full border-slate-200 rounded-xl text-sm" min="1" required />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">TAREHE YA KUZALIWA</label>
                                    <input v-model="litterForm.birth_date" type="date" class="w-full border-slate-200 rounded-xl text-xs" required />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">TAREHE YA KUACHA KUNYONYA</label>
                                <input v-model="litterForm.weaning_date" type="date" class="w-full border-slate-200 rounded-xl text-xs" />
                            </div>

                             <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500">BABA (SIRE)</label>
                                    <select v-model="litterForm.sire_code" class="w-full border-slate-200 rounded-xl text-xs bg-white">
                                        <option value="">Chagua Baba</option>
                                        <option v-for="sire in sires" :key="sire.id" :value="sire.pig_code">
                                            {{ sire.pig_code }} ({{ sire.breed }})
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500">MAMA (DAM)</label>
                                    <select v-model="litterForm.dam_code" class="w-full border-slate-200 rounded-xl text-xs bg-white">
                                        <option value="">Chagua Mama</option>
                                        <option v-for="dam in dams" :key="dam.id" :value="dam.pig_code">
                                            {{ dam.pig_code }} ({{ dam.breed }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2 pt-2">
                                <button type="submit" :disabled="litterForm.processing" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition">
                                    {{ litterForm.processing ? 'Inahifadhi...' : (isEditing ? 'Huisha Kundi' : 'Rekodi Kundi') }}
                                </button>
                                <button v-if="isEditing" type="button" @click="cancelEdit" class="w-full py-2 bg-slate-200 text-slate-700 rounded-xl font-bold text-xs uppercase">Ghairi</button>
                            </div>
                            
                        </form>
                    </div>

                    <div class="lg:col-span-3 bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <div class="flex justify-between items-center mb-6 border-b pb-4">
                            <div class="flex gap-2">
                                <button v-for="p in uniqueFarmPens" :key="p" @click="activePenFilter = p" :class="activePenFilter === p ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'" class="px-3 py-1 rounded-lg text-[10px] font-bold">{{ p }}</button>
                            </div>
                            <input v-model="searchQuery" type="text" placeholder="Tafuta..." class="border-slate-200 rounded-xl text-xs w-48" />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-slate-400 text-[10px] font-bold uppercase border-b">
                                        <th class="pb-3 px-2">ID & Umri</th>
                                        <th class="pb-3 px-2">Breed & Jinsia</th>
                                        <th class="pb-3 px-2">Banda / Idadi</th>
                                        <th class="pb-3 px-2">Wazazi & Breed zao</th>
                                        <th class="pb-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y">
                                    <tr v-for="pig in filteredPigs" :key="pig.id" :class="editingRecordId === pig.id ? 'bg-amber-50/50' : 'hover:bg-slate-50'" class="transition">
                                        <td class="py-4 px-2">
                                            <div class="font-bold text-slate-800 text-sm">
                                                {{ pig.pig_code }}
                                                <span v-if="pig.record_type === 'litter'" class="ml-1 text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-black">KUNDI</span>
                                            </div>
                                            <div class="text-[11px] text-indigo-600 font-semibold mt-0.5">Umri: {{ pig.age_manual || calculateAge(pig.birth_date) }}</div>
                                        </td>
                                        <td class="py-4 px-2">
                                            <div class="font-bold text-slate-700 text-xs uppercase">{{ pig.breed }}</div>
                                            <div class="text-slate-500 text-[11px]">{{ pig.gender }}</div>
                                        </td>
                                        <td class="py-4 px-2">
                                            <span v-if="pig.record_type === 'litter'" class="font-bold text-amber-600">👪 {{ pig.litter_size }}</span>
                                            <span v-else class="font-semibold text-slate-600">🏠 {{ pig.pen_number || '-' }}</span>
                                        </td>
                                        
                                        <td class="py-4 px-2 text-slate-500">
                                            <div v-if="pig.sire_code" class="mb-1">
                                                <span class="font-bold text-slate-700">B:</span> {{ pig.sire_code }}
                                                <span class="text-[10px] text-blue-600 block italic">
                                                    ({{ props.sires.find(s => s.pig_code === pig.sire_code)?.breed || 'Breed haijulikani' }})
                                                </span>
                                            </div>
                                            <div v-if="pig.dam_code">
                                                <span class="font-bold text-slate-700">M:</span> {{ pig.dam_code }}
                                                <span class="text-[10px] text-pink-600 block italic">
                                                    ({{ props.dams.find(d => d.pig_code === pig.dam_code)?.breed || 'Breed haijulikani' }})
                                                </span>
                                            </div>
                                            <div v-if="!pig.sire_code && !pig.dam_code" class="text-slate-400 italic text-[11px]">
                                                Haina wazazi
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button @click="editRecord(pig)" class="text-[10px] bg-blue-50 text-blue-700 px-2 py-1 rounded font-bold">Edit</button>
                                                <button @click="deleteRecord(pig.id)" class="text-[10px] bg-red-50 text-red-600 px-2 py-1 rounded font-bold">Futa</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredPigs.length === 0">
                                        <td colspan="5" class="text-center py-8 text-slate-400 font-medium">Hakuna rekodi yoyote ya nguruwe iliyopatikana kwenye ukurasa huu.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="props.records?.links?.length > 3" class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                            <div class="text-xs text-slate-500">
                                Inaonesha <span class="font-bold">{{ props.records.from || 0 }}</span> hadi <span class="font-bold">{{ props.records.to || 0 }}</span> kati ya nguruwe <span class="font-bold">{{ props.records.total || 0 }}</span>
                            </div>
                            <div class="flex gap-1">
                                <button v-for="(link, index) in props.records.links" 
                                        :key="index"
                                        @click="link.url ? router.visit(link.url, { preserveScroll: true }) : null"
                                        :disabled="!link.url"
                                        :class="[
                                            link.active ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                                            !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
                                        ]"
                                        class="px-3 py-1.5 rounded-lg text-xs transition"
                                        v-html="link.label">
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>