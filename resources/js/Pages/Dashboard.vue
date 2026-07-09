<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Doughnut, Bar } from 'vue-chartjs';
import { 
    Chart as ChartJS, 
    Title, 
    Tooltip, 
    Legend, 
    LinearScale, 
    CategoryScale, 
    ArcElement,
    BarElement
} from 'chart.js';

// Sajili vifaa vyote vya Chart.js vinavyohitajika
ChartJS.register(Title, Tooltip, Legend, LinearScale, CategoryScale, ArcElement, BarElement);

const props = defineProps({
    stats: Object,
    breedChartData: Object,
    recentActivities: Array
});

// 1. Grafu ya Nguzo ya Wima (Idadi ya Makundi - Vertical Bar Chart)
const barChartData = computed(() => ({
    labels: ['Madume (Boars)', 'Majike (Sows)', 'Watoto (Weaners)'],
    datasets: [
        {
            label: 'Idadi Halisi',
            backgroundColor: ['rgba(59, 130, 246, 0.85)', 'rgba(236, 72, 153, 0.85)', 'rgba(245, 158, 11, 0.85)'],
            borderRadius: 8,
            data: [props.stats?.boars || 0, props.stats?.sows || 0, props.stats?.weaners || 0]
        }
    ]
}));

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } }
    }
};

// 2. Grafu ya Nguzo ya Kulazwa (Mchanganuo wa Breeds - Horizontal Bar Chart yenye Rangi Tofauti)
// 2. Grafu ya Nguzo ya Kulazwa (Mchanganuo wa Breeds - Horizontal Bar Chart)
const breedChartDataConfig = computed(() => ({
    labels: props.breedChartData?.labels || [],
    datasets: [
        {
            label: 'Idadi ya Nguruwe',
            // Tumeweka array ya rangi tofauti kwa kila nguzo ili zitofautiane kwa muonekano
            backgroundColor: [
                'rgba(16, 185, 129, 0.85)',  // Emerald/Kijani (Ukoo wa 1)
                'rgba(99, 102, 241, 0.85)',  // Indigo (Ukoo wa 2)
                'rgba(245, 158, 11, 0.85)',  // Amber/Njano (Ukoo wa 3)
                'rgba(239, 68, 68, 0.85)',   // Red/Nyekundu (Ukoo wa 4)
                'rgba(14, 165, 233, 0.85)',  // Sky Blue/Bluu (Ukoo wa 5)
                'rgba(168, 85, 247, 0.85)',  // Purple/Urujuani (Ukoo wa 6)
            ],
            borderColor: [
                '#10b981',
                '#6366f1',
                '#f59e0b',
                '#ef4444',
                '#0ea5e9',
                '#a855f7'
            ],
            borderWidth: 1,
            borderRadius: 6,
            data: props.breedChartData?.dataset || []
        }
    ]
}));

const breedChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y', // Inalaza nguzo
    plugins: { legend: { display: false } },
    scales: {
        x: { beginAtZero: true, ticks: { precision: 0 } },
        y: { grid: { display: false } }
    }
};

// 3. Grafu ya Duara (Uwiano kwa Asilimia - Doughnut Chart)
const doughnutChartData = computed(() => ({
    labels: ['Madume', 'Majike', 'Watoto'],
    datasets: [
        {
            backgroundColor: ['#3b82f6', '#ec4899', '#f59e0b'],
            data: [props.stats?.boars || 0, props.stats?.sows || 0, props.stats?.weaners || 0]
        }
    ]
}));

const doughnutChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
};

const formatTimeAgo = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('sw-TZ', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-xl text-slate-800 leading-tight">Meneja wa Shamba</h2>
                <span class="text-xs font-semibold bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg border border-indigo-100">
                    Mfumo Uko Hai 🟢
                </span>
            </div>
        </template>

        <div class="py-6 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Habari, {{ $page.props.auth.user.name }}!</h3>
                        <p class="text-slate-500 text-xs mt-0.5">Muonekano wa sasa wa vizazi, makundi na takwimu za mifugo.</p>
                    </div>
                    <Link :href="route('records.index')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm shrink-0">
                        + Kusajili Mnyama Mpya
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-indigo-600 border-t border-r border-b border-slate-200 flex items-center gap-4">
                        <div class="bg-indigo-50 text-indigo-600 p-3 rounded-full text-lg shrink-0">🐖</div>
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Jumla Shambani (Hai)</p>
                            <h3 class="text-2xl font-black text-slate-800 leading-tight">{{ props.stats?.total_pigs || 0 }}</h3>
                            <p v-if="props.stats?.deceased" class="text-[10px] text-red-500 font-semibold mt-0.5">
                                {{ props.stats.deceased }} waliofariki
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 border-t border-r border-b border-slate-200 flex items-center gap-4">
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-full text-lg shrink-0">🦾</div>
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Madume (Boars)</p>
                            <h3 class="text-2xl font-black text-slate-800 leading-tight">{{ props.stats?.boars || 0 }}</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-pink-500 border-t border-r border-b border-slate-200 flex items-center gap-4">
                        <div class="bg-pink-50 text-pink-600 p-3 rounded-full text-lg shrink-0">🎀</div>
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Majike (Sows)</p>
                            <h3 class="text-2xl font-black text-slate-800 leading-tight">{{ props.stats?.sows || 0 }}</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-amber-500 border-t border-r border-b border-slate-200 flex items-center gap-4">
                        <div class="bg-amber-50 text-amber-600 p-3 rounded-full text-lg shrink-0">🍼</div>
                        <div>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Watoto / Vikundi</p>
                            <h3 class="text-2xl font-black text-slate-800 leading-tight">{{ props.stats?.weaners || 0 }}</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Idadi kwa Makundi</h4>
                            <p class="text-slate-400 text-[10px]">Ulinganisho wa makundi makuu ya mifugo</p>
                        </div>
                        <div class="h-56 relative">
                            <Bar :data="barChartData" :options="barChartOptions" />
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Mchanganuo wa Breeds (Ukoo)</h4>
                            <p class="text-slate-400 text-[10px]">Idadi ya nguruwe kulingana na chapa/ukoo wao</p>
                        </div>
                        <div class="h-56 relative">
                            <Bar :data="breedChartDataConfig" :options="breedChartOptions" />
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Uwiano wa Shamba</h4>
                            <p class="text-slate-400 text-[10px]">Asilimia ya mgawanyo wa jumla (%)</p>
                        </div>
                        <div class="h-56 relative flex items-center justify-center">
                            <Doughnut :data="doughnutChartData" :options="doughnutChartOptions" />
                        </div>
                    </div>

                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                    <div class="flex justify-between items-center border-b pb-3">
                        <h4 class="font-bold text-sm text-slate-800 uppercase tracking-wider">Sajili za Hivi Karibuni</h4>
                        <Link :href="route('records.index')" class="text-xs text-indigo-600 font-semibold hover:underline">Ona Zote →</Link>
                    </div>

                    <div v-if="props.recentActivities?.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-bold uppercase bg-slate-50">
                                    <th class="p-3">ID / Code</th>
                                    <th class="p-3">Aina</th>
                                    <th class="p-3">Ukoo (Breed)</th>
                                    <th class="p-3 text-right">Muda wa Kuandikishwa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <tr v-for="(act, i) in props.recentActivities" :key="i" class="hover:bg-slate-50/80 transition text-slate-700">
                                    <td class="p-3 font-bold text-indigo-600">{{ act.pig_code }}</td>
                                    <td class="p-3">
                                        <span :class="act.record_type === 'litter' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-blue-50 text-blue-700 border-blue-100'" class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border">
                                            {{ act.record_type }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-medium">{{ act.breed || 'Haifahamiki' }}</td>
                                    <td class="p-3 text-right text-slate-400">{{ formatTimeAgo(act.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-6 text-slate-400 italic text-xs">
                        Hakuna rekodi mpya kwa sasa.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>