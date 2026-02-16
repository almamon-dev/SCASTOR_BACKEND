import AdminLayout from "@/Layouts/AdminLayout";
import { Head } from "@inertiajs/react";
import {
    Briefcase,
    Code,
    GraduationCap,
    Mail,
    Clock,
    BarChart3,
    ArrowUpRight,
    Zap,
} from "lucide-react";

export default function Dashboard({
    auth,
    stats,
    recentProjects,
    portfolioHealth,
}) {
    const user = auth.user;

    return (
        <AdminLayout>
            <Head title="Dashboard" />

            <div className="space-y-8">
                {/* Welcome Banner */}
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-3xl relative border border-gray-100">
                    <div className="absolute top-0 right-0 w-64 h-64 bg-purple-50 rounded-full -mr-32 -mt-32 opacity-50 blur-3xl"></div>
                    <div className="p-8 sm:p-10 relative z-10 flex flex-col lg:flex-row justify-between lg:items-center gap-8">
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <div className="bg-purple-600 text-white p-1.5 rounded-lg shadow-lg shadow-purple-100">
                                    <Zap size={18} fill="currentColor" />
                                </div>
                                <span className="text-purple-700 font-black text-xs uppercase tracking-widest">
                                    PORTFOLIO INSIGHTS
                                </span>
                            </div>
                            <h1 className="text-4xl font-extrabold text-gray-900 mb-3 tracking-tight">
                                Welcome back,{" "}
                                <span className="text-purple-600">
                                    {user.name.split(" ")[0]}!
                                </span>
                            </h1>
                            <p className="text-gray-500 max-w-lg leading-relaxed font-medium">
                                Your portfolio is looking amazing today. You've
                                completed{" "}
                                <span className="text-gray-900 font-bold">
                                    12 projects
                                </span>{" "}
                                so far. Keep up the great work!
                            </p>
                        </div>

                        <div className="flex bg-gray-50/50 backdrop-blur-sm rounded-2xl p-4 gap-8 border border-gray-100 self-start lg:self-center">
                            <div className="text-center">
                                <div className="text-[10px] uppercase text-gray-400 font-black tracking-widest mb-1">
                                    PROJECTS
                                </div>
                                <div className="text-3xl font-black text-emerald-500">
                                    {stats.projects.count}
                                </div>
                            </div>
                            <div className="w-px h-12 bg-gray-200"></div>
                            <div className="text-center">
                                <div className="text-[10px] uppercase text-gray-400 font-black tracking-widest mb-1">
                                    SKILLS
                                </div>
                                <div className="text-3xl font-black text-purple-600">
                                    {stats.skills.count}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {[
                        {
                            label: "Total Projects",
                            value: stats.projects.count,
                            status: stats.projects.status,
                            icon: Briefcase,
                            color: "purple",
                        },
                        {
                            label: "Total Skills",
                            value: stats.skills.count,
                            status: stats.skills.status,
                            icon: Code,
                            color: "emerald",
                        },
                        {
                            label: "Experiences",
                            value: stats.experiences.count,
                            status: stats.experiences.status,
                            icon: GraduationCap,
                            color: "yellow",
                        },
                        {
                            label: "Total Contacts",
                            value: stats.contacts.count,
                            status: stats.contacts.status,
                            icon: Mail,
                            color: "red",
                        },
                    ].map((item, idx) => (
                        <div
                            key={idx}
                            className="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                        >
                            <div className="flex justify-between items-start mb-6">
                                <div
                                    className={`p-4 bg-${item.color}-50 rounded-2xl`}
                                >
                                    <item.icon
                                        className={`text-${item.color}-600 w-6 h-6`}
                                    />
                                </div>
                                <div className="flex items-center text-emerald-500 text-[10px] font-black bg-emerald-50 px-2 py-1 rounded-full gap-1">
                                    <ArrowUpRight className="w-3 h-3" />
                                    <span>{item.status}</span>
                                </div>
                            </div>
                            <div>
                                <div className="text-[10px] uppercase text-gray-400 font-black tracking-widest mb-1">
                                    {item.label}
                                </div>
                                <div className="text-3xl font-black text-gray-900">
                                    {item.value}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Main Content Area */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Recent Projects Table */}
                    <div className="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-8 flex items-center justify-between border-b border-gray-50">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-gray-50 rounded-xl">
                                    <Clock className="w-4 h-4 text-gray-400" />
                                </div>
                                <h3 className="font-extrabold text-gray-900 tracking-tight">
                                    Recent Projects
                                </h3>
                            </div>
                            <button className="text-xs font-black text-purple-600 hover:text-purple-700 uppercase tracking-widest transition-colors">
                                View All
                            </button>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="bg-gray-50/50">
                                        <th className="py-4 pl-8 text-[10px] uppercase text-gray-400 font-black tracking-widest">
                                            Project Name
                                        </th>
                                        <th className="py-4 text-center text-[10px] uppercase text-gray-400 font-black tracking-widest">
                                            Category
                                        </th>
                                        <th className="py-4 pr-8 text-right text-[10px] uppercase text-gray-400 font-black tracking-widest">
                                            Last Updated
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {recentProjects.map((project) => (
                                        <tr
                                            key={project.id}
                                            className="hover:bg-purple-50/20 group transition-all duration-200 cursor-pointer"
                                        >
                                            <td className="py-4 pl-8">
                                                <div className="flex items-center gap-4">
                                                    <div
                                                        className={`w-10 h-10 rounded-xl ${project.color} flex items-center justify-center font-black text-xs shadow-sm`}
                                                    >
                                                        {project.initials}
                                                    </div>
                                                    <div className="font-extrabold text-gray-900 text-sm group-hover:text-purple-600 transition-colors">
                                                        {project.title}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="py-4 text-center">
                                                <span className="inline-block px-3 py-1 rounded-full bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-widest">
                                                    {project.category}
                                                </span>
                                            </td>
                                            <td className="py-4 text-right pr-8">
                                                <span className="text-xs text-gray-400 font-bold italic">
                                                    {project.created_at}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Sidebar Column */}
                    <div className="space-y-8">
                        {/* Portfolio Health Card */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            <div className="flex items-center justify-between mb-8">
                                <h3 className="font-extrabold text-gray-900 tracking-tight">
                                    Portfolio Health
                                </h3>
                                <BarChart3 className="w-5 h-5 text-gray-300" />
                            </div>

                            <div className="space-y-8">
                                <div>
                                    <div className="flex justify-between items-end mb-3">
                                        <span className="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                            Profile Completion
                                        </span>
                                        <span className="text-xs font-black text-purple-600">
                                            {portfolioHealth.completion}%
                                        </span>
                                    </div>
                                    <div className="h-3 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                                        <div
                                            className="h-full bg-gradient-to-r from-purple-500 to-purple-700 rounded-full transition-all duration-1000"
                                            style={{
                                                width: `${portfolioHealth.completion}%`,
                                            }}
                                        ></div>
                                    </div>
                                </div>

                                <div>
                                    <div className="flex justify-between items-end mb-3">
                                        <span className="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                            Experience Power
                                        </span>
                                        <span className="text-xs font-black text-emerald-500">
                                            {portfolioHealth.activeExperiences}
                                        </span>
                                    </div>
                                    <div className="h-3 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                                        <div className="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full w-3/4"></div>
                                    </div>
                                </div>

                                <div>
                                    <div className="flex justify-between items-end mb-3">
                                        <span className="text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                            Skill Verification
                                        </span>
                                        <span className="text-xs font-black text-orange-500">
                                            {portfolioHealth.verifiedSkills}
                                        </span>
                                    </div>
                                    <div className="h-3 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                                        <div className="h-full bg-gradient-to-r from-orange-400 to-orange-600 rounded-full w-4/5"></div>
                                    </div>
                                </div>
                            </div>

                            <button className="w-full mt-10 py-3 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-800 transition-all shadow-xl shadow-gray-200">
                                Update Portfolio
                            </button>
                        </div>

                        {/* Action Card */}
                        <div className="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                            <div className="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <h4 className="text-lg font-black mb-2 italic">
                                Need help?
                            </h4>
                            <p className="text-white/70 text-xs font-medium mb-6 leading-relaxed">
                                Our support team is available 24/7 to help you
                                with your portfolio management.
                            </p>
                            <button className="bg-white text-purple-700 px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:bg-gray-50 transition-colors">
                                Contact Support
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
