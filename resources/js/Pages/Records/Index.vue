<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    records: Object,
    sires: Array,
    dams: Array,
    stats: Object,
    isAdmin: Boolean,
});

// ------- State Management -------
const showAlert = ref(false);
const searchQuery = ref('');
const activePenFilter = ref('Wote');
const activeCategoryFilter = ref('Wote');
const activeFormTab = ref('pig');

// Hali ya kuhariri (Edit Mode)
const isEditing = ref(false);
const editingRecordId = ref(null);

// Modal ya uzito
const weightModalOpen = ref(false);
const weightTarget = ref(null);

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);

watch(flashMessage, (newMessage) => {
    if (newMessage) {
        showAlert.value = true;
        setTimeout(() => { showAlert.value = false; }, 4000);
    }
}, { immediate: true });

// ------- Fomu -------
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
    dam_code: '',
});

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
    dam_code: '',
});

const weightForm = useForm({ new_weight: '' });

// ------- Helpers -------
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
        return remMonths ? `${years} Mwaka ${remMonths} Miezi` : `${years} Mwaka`;
    }
    return `${months} Miezi`;
};

const statusStyle = (status) => {
    if (status === 'Aliekufa') return 'bg-red-100 text-red-700';
    if (status === 'Mzazi') return 'bg-purple-100 text-purple-700';
    if (status === 'Mtoto') return 'bg-amber-100 text-amber-700';
    return 'bg-emerald-100 text-emerald-700';
};

// ------- Kuchuja data -------
const filteredPigs = computed(() => {
    const recordList = props.records?.data || [];
    return recordList.filter(pig => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = !q
            || pig.pig_code?.toLowerCase().includes(q)
            || pig.breed?.toLowerCase().includes(q)
            || pig.title?.toLowerCase().includes(q);
        const matchesPen = activePenFilter.value === 'Wote' || pig.pen_number === activePenFilter.value;

        let matchesCategory = true;
        if (activeCategoryFilter.value === 'Boars') matchesCategory = pig.gender === 'Dume' && pig.record_type !== 'litter';
        if (activeCategoryFilter.value === 'Sows') matchesCategory = pig.gender === 'Jike' && pig.record_type !== 'litter';
        if (activeCategoryFilter.value === 'Weaners') matchesCategory = pig.status === 'Mtoto' || pig.record_type === 'litter';
        if (activeCategoryFilter.value === 'Deceased') matchesCategory = pig.status === 'Aliekufa';

        // Ficha waliokufa isipokuwa unapowachuja mahususi
        const matchesLife = activeCategoryFilter.value === 'Deceased' || pig.status !== 'Aliekufa';

        return matchesSearch && matchesPen && matchesCategory && matchesLife;
    });
});

const selectedSireData = computed(() => props.sires.find(s => s.pig_code === litterForm.sire_code));
const selectedDamData = computed(() => props.dams.find(d => d.pig_code === litterForm.dam_code));

watch(() => litterForm.birth_date, (newDate) => {
    if (newDate && !isEditing.value) {
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

// ------- Edit -------
const editRecord = (pig) => {
    isEditing.value = true;
    editingRecordId.value = pig.id;
    activeFormTab.value = pig.record_type;
    window.scrollTo({ top: 0, behavior: 'smooth' });

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
        pigForm.sire_code = pig.sire_code || '';
        pigForm.dam_code = pig.dam_code || '';
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
    pigForm.clearErrors();
    litterForm.reset();
    litterForm.clearErrors();
};

const deleteRecord = (id) => {
    if (confirm('Je, una uhakika unataka kufuta kabisa rekodi hii ya nguruwe?')) {
        router.delete(route('records.destroy', id), {
            preserveScroll: true,
            onSuccess: () => { if (editingRecordId.value === id) cancelEdit(); },
        });
    }
};

const markDeceased = (pig) => {
    if (!confirm(`Thibitisha kuwa nguruwe "${pig.pig_code}" amefariki? Ataondolewa kwenye takwimu za wanyama hai.`)) return;
    router.put(route('records.update', pig.id), {
        record_type: pig.record_type,
        pig_code: pig.pig_code,
        birth_date: pig.birth_date,
        breed: pig.breed,
        gender: pig.gender,
        pen_number: pig.pen_number,
        litter_size: pig.litter_size,
        status: 'Aliekufa',
    }, { preserveScroll: true });
};

// ------- Submit -------
const submitPig = () => {
    if (isEditing.value) {
        pigForm.put(route('records.update', editingRecordId.value), {
            preserveScroll: true,
            onSuccess: () => cancelEdit(),
        });
    } else {
        pigForm.post(route('records.store'), {
            preserveScroll: true,
            onSuccess: () => pigForm.reset(),
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
            onSuccess: () => cancelEdit(),
        });
    } else {
        litterForm.post(route('records.store'), {
            preserveScroll: true,
            onSuccess: () => litterForm.reset(),
        });
    }
};

// ------- Weight tracking -------
const weightHistory = computed(() => {
    const h = weightTarget.value?.weight_history;
    if (!Array.isArray(h)) return [];
    return [...h].reverse();
});

const weightTrend = computed(() => {
    const h = weightTarget.value?.weight_history;
    if (!Array.isArray(h) || h.length < 2) return null;
    const diff = Number(h[h.length - 1].weight) - Number(h[0].weight);
    return { diff, up: diff >= 0 };
});

const openWeightModal = (pig) => {
    weightTarget.value = pig;
    weightForm.reset();
    weightForm.clearErrors();
    weightModalOpen.value = true;
};

const closeWeightModal = () => {
    weightModalOpen.value = false;
};

const submitWeight = () => {
    weightForm.put(route('records.updateWeight', weightTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            const updated = (props.records?.data || []).find(r => r.id === weightTarget.value.id);
            if (updated) weightTarget.value = updated;
            weightForm.reset();
        },
    });
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

                <!-- Flash message -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showAlert" class="mb-4 flex items-center gap-2 bg-emerald-600 text-white text-sm font-semibold px-4 py-3 rounded-xl shadow-lg">
                        <span class="text-lg">✓</span> {{ flashMessage }}
                    </div>
                </Transition>

                <!-- Stat cards / filters -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                    <div @click="activeCategoryFilter = 'Wote'" :class="activeCategoryFilter === 'Wote' ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-[10px] text-slate-500 font-bold uppercase">Jumla Hai</p>
                        <h3 class="text-2xl font-black text-indigo-600">{{ props.stats?.total_pigs || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Boars'" :class="activeCategoryFilter === 'Boars' ? 'ring-2 ring-blue-500 bg-blue-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-[10px] text-slate-500 font-bold uppercase">Madume</p>
                        <h3 class="text-2xl font-black text-blue-600">{{ props.stats?.boars || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Sows'" :class="activeCategoryFilter === 'Sows' ? 'ring-2 ring-pink-500 bg-pink-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-[10px] text-slate-500 font-bold uppercase">Majike</p>
                        <h3 class="text-2xl font-black text-pink-600">{{ props.stats?.sows || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Weaners'" :class="activeCategoryFilter === 'Weaners' ? 'ring-2 ring-amber-500 bg-amber-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-[10px] text-slate-500 font-bold uppercase">Watoto</p>
                        <h3 class="text-2xl font-black text-amber-600">{{ props.stats?.weaners || 0 }}</h3>
                    </div>
                    <div @click="activeCategoryFilter = 'Deceased'" :class="activeCategoryFilter === 'Deceased' ? 'ring-2 ring-red-500 bg-red-50' : 'bg-white'" class="p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition">
                        <p class="text-[10px] text-slate-500 font-bold uppercase">Waliofariki</p>
                        <h3 class="text-2xl font-black text-red-500">{{ props.stats?.deceased || 0 }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

                    <!-- Form panel -->
                    <div :class="isEditing ? 'ring-2 ring-amber-400 bg-amber-50/20' : 'bg-white'"
                         class="lg:col-span-1 p-4 rounded-2xl shadow-sm border border-slate-200 lg:sticky lg:top-6 max-h-[calc(100vh-120px)] overflow-y-auto transition-all">

                        <div v-if="!isEditing" class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl mb-4 text-[10px]">
                            <button @click="activeFormTab = 'pig'" :class="activeFormTab === 'pig' ? 'bg-white shadow-sm font-bold' : ''" class="py-2 rounded-lg">NGURUWE MKUU</button>
                            <button @click="activeFormTab = 'litter'" :class="activeFormTab === 'litter' ? 'bg-white shadow-sm font-bold' : ''" class="py-2 rounded-lg">KUNDI LA WATOTO</button>
                        </div>
                        <div v-else class="text-center bg-amber-100 text-amber-900 font-black text-[10px] py-2 rounded-lg mb-4 uppercase tracking-wider">
                            Unahariri: {{ activeFormTab === 'pig' ? 'Nguruwe Mkuu' : 'Kundi la Watoto' }}
                        </div>

                        <!-- PIG FORM -->
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
                                    <span v-if="pigForm.errors.gender" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.gender }}</span>
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
                                        <option value="Aliekufa">Amefariki</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600">TAREHE YA KUZALIWA</label>
                                <input v-model="pigForm.birth_date" type="date" class="w-full border-slate-200 rounded-xl text-xs" required />
                                <span v-if="pigForm.errors.birth_date" class="text-xs text-red-500 font-bold block mt-1">{{ pigForm.errors.birth_date }}</span>
                            </div>
                            <div class="space-y-2 pt-2">
                                <button type="submit" :disabled="pigForm.processing" :class="isEditing ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-700'" class="w-full py-3 text-white rounded-xl font-bold text-xs uppercase transition disabled:opacity-60">
                                    {{ pigForm.processing ? 'Inahifadhi...' : (isEditing ? 'Huisha Nguruwe' : 'Hifadhi Nguruwe') }}
                                </button>
                                <button v-if="isEditing" type="button" @click="cancelEdit" class="w-full py-2 bg-slate-200 text-slate-700 rounded-xl font-bold text-xs uppercase">Ghairi</button>
                            </div>
                        </form>

                        <!-- LITTER FORM -->
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
                                    <span v-if="litterForm.errors.litter_size" class="text-xs text-red-500 font-bold block mt-1">{{ litterForm.errors.litter_size }}</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600">TAREHE YA KUZALIWA</label>
                                    <input v-model="litterForm.birth_date" type="date" class="w-full border-slate-200 rounded-xl text-xs" required />
                                    <span v-if="litterForm.errors.birth_date" class="text-xs text-red-500 font-bold block mt-1">{{ litterForm.errors.birth_date }}</span>
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
                                <button type="submit" :disabled="litterForm.processing" :class="isEditing ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-700'" class="w-full py-3 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition disabled:opacity-60">
                                    {{ litterForm.processing ? 'Inahifadhi...' : (isEditing ? 'Huisha Kundi' : 'Rekodi Kundi') }}
                                </button>
                                <button v-if="isEditing" type="button" @click="cancelEdit" class="w-full py-2 bg-slate-200 text-slate-700 rounded-xl font-bold text-xs uppercase">Ghairi</button>
                            </div>
                        </form>
                    </div>

                    <!-- Table panel -->
                    <div class="lg:col-span-3 bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <div class="flex flex-wrap gap-3 justify-between items-center mb-6 border-b pb-4">
                            <div class="flex flex-wrap gap-2">
                                <button v-for="p in uniqueFarmPens" :key="p" @click="activePenFilter = p" :class="activePenFilter === p ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'" class="px-3 py-1 rounded-lg text-[10px] font-bold">{{ p }}</button>
                            </div>
                            <input v-model="searchQuery" type="text" placeholder="Tafuta ID, breed, jina..." class="border-slate-200 rounded-xl text-xs w-56" />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-slate-400 text-[10px] font-bold uppercase border-b">
                                        <th class="pb-3 px-2">ID & Umri</th>
                                        <th class="pb-3 px-2">Breed & Jinsia</th>
                                        <th class="pb-3 px-2">Banda / Idadi</th>
                                        <th class="pb-3 px-2">Uzito</th>
                                        <th class="pb-3 px-2">Wazazi</th>
                                        <th class="pb-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y">
                                    <tr v-for="pig in filteredPigs" :key="pig.id" :class="[editingRecordId === pig.id ? 'bg-amber-50/50' : 'hover:bg-slate-50', pig.status === 'Aliekufa' ? 'opacity-60' : '']" class="transition">
                                        <td class="py-4 px-2">
                                            <div class="font-bold text-slate-800 text-sm flex items-center gap-1 flex-wrap">
                                                {{ pig.pig_code }}
                                                <span v-if="pig.record_type === 'litter'" class="text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-black">KUNDI</span>
                                                <span :class="statusStyle(pig.status)" class="text-[9px] px-1.5 py-0.5 rounded font-black">{{ pig.status === 'Aliekufa' ? 'AMEFARIKI' : pig.status }}</span>
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
                                        <td class="py-4 px-2">
                                            <button @click="openWeightModal(pig)" class="group flex items-center gap-1 text-left">
                                                <span v-if="pig.value" class="font-black text-slate-800 text-sm">{{ pig.value }}<span class="text-[10px] font-medium text-slate-400"> kg</span></span>
                                                <span v-else class="text-[11px] text-slate-400 italic group-hover:text-indigo-600">Weka uzito +</span>
                                            </button>
                                        </td>
                                        <td class="py-4 px-2 text-slate-500">
                                            <div v-if="pig.sire_code" class="mb-1">
                                                <span class="font-bold text-slate-700">B:</span> {{ pig.sire_code }}
                                                <span class="text-[10px] text-blue-600 block italic">
                                                    ({{ props.sires.find(s => s.pig_code === pig.sire_code)?.breed || 'Haijulikani' }})
                                                </span>
                                            </div>
                                            <div v-if="pig.dam_code">
                                                <span class="font-bold text-slate-700">M:</span> {{ pig.dam_code }}
                                                <span class="text-[10px] text-pink-600 block italic">
                                                    ({{ props.dams.find(d => d.pig_code === pig.dam_code)?.breed || 'Haijulikani' }})
                                                </span>
                                            </div>
                                            <div v-if="!pig.sire_code && !pig.dam_code" class="text-slate-400 italic text-[11px]">
                                                Haina wazazi
                                            </div>
                                        </td>
                                        <td class="py-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button @click="openWeightModal(pig)" class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-1 rounded font-bold" title="Rekodi uzito">Uzito</button>
                                                <button @click="editRecord(pig)" class="text-[10px] bg-blue-50 text-blue-700 px-2 py-1 rounded font-bold">Edit</button>
                                                <button v-if="pig.status !== 'Aliekufa'" @click="markDeceased(pig)" class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded font-bold" title="Weka amefariki">Fariki</button>
                                                <button @click="deleteRecord(pig.id)" class="text-[10px] bg-red-50 text-red-600 px-2 py-1 rounded font-bold">Futa</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredPigs.length === 0">
                                        <td colspan="6" class="text-center py-10 text-slate-400 font-medium">
                                            <div class="text-3xl mb-2">🐖</div>
                                            Hakuna rekodi yoyote iliyopatikana kwenye ukurasa huu.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="props.records?.links?.length > 3" class="mt-6 flex flex-wrap gap-3 items-center justify-between border-t border-slate-100 pt-4">
                            <div class="text-xs text-slate-500">
                                Inaonesha <span class="font-bold">{{ props.records.from || 0 }}</span> hadi <span class="font-bold">{{ props.records.to || 0 }}</span> kati ya <span class="font-bold">{{ props.records.total || 0 }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <button v-for="(link, index) in props.records.links"
                                        :key="index"
                                        @click="link.url ? router.visit(link.url, { preserveScroll: true }) : null"
                                        :disabled="!link.url"
                                        :class="[
                                            link.active ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                                            !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer',
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

        <!-- Weight modal -->
        <Modal :show="weightModalOpen" @close="closeWeightModal" max-width="lg">
            <div v-if="weightTarget" class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-black text-lg text-slate-800">Rekodi ya Uzito</h3>
                        <p class="text-xs text-slate-500">{{ weightTarget.pig_code }} · {{ weightTarget.breed }}</p>
                    </div>
                    <button @click="closeWeightModal" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
                </div>

                <div class="flex items-end gap-4 bg-slate-50 rounded-xl p-4 mb-4 border border-slate-100">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400">Uzito wa sasa</p>
                        <p class="text-3xl font-black text-emerald-600">{{ weightTarget.value || 0 }}<span class="text-sm font-medium text-slate-400"> kg</span></p>
                    </div>
                    <div v-if="weightTrend" :class="weightTrend.up ? 'text-emerald-600' : 'text-red-500'" class="text-xs font-bold pb-1">
                        {{ weightTrend.up ? '▲' : '▼' }} {{ Math.abs(weightTrend.diff).toFixed(1) }} kg tangu mwanzo
                    </div>
                </div>

                <form @submit.prevent="submitWeight" class="flex gap-2 mb-4">
                    <input v-model="weightForm.new_weight" type="number" step="0.1" min="1" placeholder="Uzito mpya (kg)" class="flex-1 border-slate-200 rounded-xl text-sm" required />
                    <button type="submit" :disabled="weightForm.processing" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 rounded-xl uppercase disabled:opacity-60">
                        {{ weightForm.processing ? '...' : 'Ongeza' }}
                    </button>
                </form>
                <span v-if="weightForm.errors.new_weight" class="text-xs text-red-500 font-bold block -mt-2 mb-3">{{ weightForm.errors.new_weight }}</span>

                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 mb-2">Historia ya Uzito</p>
                    <div v-if="weightHistory.length" class="max-h-48 overflow-y-auto divide-y divide-slate-100 border border-slate-100 rounded-xl">
                        <div v-for="(entry, i) in weightHistory" :key="i" class="flex justify-between items-center px-3 py-2 text-xs">
                            <span class="text-slate-500">{{ entry.date }}</span>
                            <span class="font-bold text-slate-700">{{ entry.weight }} kg</span>
                        </div>
                    </div>
                    <p v-else class="text-xs text-slate-400 italic text-center py-4">Bado hakuna historia ya uzito iliyorekodiwa.</p>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
