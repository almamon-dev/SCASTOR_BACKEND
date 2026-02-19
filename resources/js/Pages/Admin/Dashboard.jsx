import AdminLayout from "@/Layouts/AdminLayout";
import { Head } from "@inertiajs/react";
import {
    Utensils,
    Layers,
    Users,
    Heart,
    Copy,
    Eye,
    EyeOff,
    Check,
} from "lucide-react";
import { useState } from "react";

export default function Dashboard({
    auth,
    stats,
    recentRecipes,
    openai_api_key,
}) {
    const user = auth.user;
    const [showKey, setShowKey] = useState(false);
    const [copied, setCopied] = useState(false);

    const handleCopy = () => {
        if (openai_api_key) {
            navigator.clipboard.writeText(openai_api_key);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    return (
        <AdminLayout>
            <Head title="Dashboard" />

            <div className="space-y-8 pb-20">
                {/* Minimal Header */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 tracking-tight">
                            Overview
                        </h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Welcome back, {user.name}. Here's what's happening
                            today.
                        </p>
                    </div>
                </div>

                {/* Stats Grid - Minimal */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {[
                        {
                            label: "Total Recipes",
                            value: stats.recipes.count,
                            icon: Utensils,
                        },
                        {
                            label: "Categories",
                            value: stats.categories.count,
                            icon: Layers,
                        },
                        {
                            label: "Users",
                            value: stats.users.count,
                            icon: Users,
                        },
                        {
                            label: "Favorites",
                            value: stats.favorites.count,
                            icon: Heart,
                        },
                    ].map((item, idx) => (
                        <div
                            key={idx}
                            className="bg-white p-6 rounded-xl border border-gray-100 shadow-sm"
                        >
                            <div className="flex items-center justify-between mb-4">
                                <span className="text-sm font-medium text-gray-500">
                                    {item.label}
                                </span>
                                <item.icon className="w-5 h-5 text-gray-400" />
                            </div>
                            <div className="text-2xl font-bold text-gray-900">
                                {item.value}
                            </div>
                        </div>
                    ))}
                </div>

                {/* Main Content Area */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Recent Recipes Table - Clean */}
                    <div className="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="p-6 border-b border-gray-50">
                            <h3 className="font-semibold text-gray-900">
                                Recent Recipes
                            </h3>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead className="bg-gray-50/50 text-gray-500">
                                    <tr>
                                        <th className="py-3 px-6 font-medium">
                                            Recipe
                                        </th>
                                        <th className="py-3 px-6 font-medium">
                                            Category
                                        </th>
                                        <th className="py-3 px-6 text-right font-medium">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {recentRecipes.length > 0 ? (
                                        recentRecipes.map((recipe) => (
                                            <tr
                                                key={recipe.id}
                                                className="hover:bg-gray-50/50 transition-colors"
                                            >
                                                <td className="py-3 px-6 text-gray-900 font-medium">
                                                    {recipe.title}
                                                </td>
                                                <td className="py-3 px-6 text-gray-500">
                                                    {recipe.category}
                                                </td>
                                                <td className="py-3 px-6 text-right text-gray-400">
                                                    {recipe.created_at}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td
                                                colSpan="3"
                                                className="py-8 text-center text-gray-500"
                                            >
                                                No recipes yet.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Sidebar Column */}
                    <div className="space-y-6">
                        {/* API Key Card */}
                        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h3 className="font-semibold text-gray-900 mb-4">
                                OpenAI API Key
                            </h3>
                            <div className="space-y-4">
                                <div className="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div className="flex items-center justify-between mb-2">
                                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                            Current Key
                                        </span>
                                        <div className="flex gap-2">
                                            <button
                                                onClick={() =>
                                                    setShowKey(!showKey)
                                                }
                                                className="p-1 hover:bg-gray-200 rounded text-gray-500 transition-colors"
                                                title={
                                                    showKey
                                                        ? "Hide Key"
                                                        : "Show Key"
                                                }
                                            >
                                                {showKey ? (
                                                    <EyeOff size={14} />
                                                ) : (
                                                    <Eye size={14} />
                                                )}
                                            </button>
                                            <button
                                                onClick={handleCopy}
                                                className="p-1 hover:bg-gray-200 rounded text-gray-500 transition-colors"
                                                title="Copy Key"
                                            >
                                                {copied ? (
                                                    <Check
                                                        size={14}
                                                        className="text-emerald-500"
                                                    />
                                                ) : (
                                                    <Copy size={14} />
                                                )}
                                            </button>
                                        </div>
                                    </div>
                                    <div className="font-mono text-xs text-gray-700 break-all">
                                        {openai_api_key ? (
                                            showKey ? (
                                                openai_api_key
                                            ) : (
                                                "sk-..." +
                                                openai_api_key.slice(-4)
                                            )
                                        ) : (
                                            <span className="text-gray-400 italic">
                                                No API Key configured
                                            </span>
                                        )}
                                    </div>
                                </div>
                                <p className="text-xs text-gray-500">
                                    This key is used for AI-powered features.
                                    Keep it secure.
                                </p>
                            </div>
                        </div>

                        {/* Simple System Status */}
                        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h3 className="font-semibold text-gray-900 mb-6">
                                Platform Status
                            </h3>

                            <div className="space-y-6">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <span className="text-sm text-gray-600">
                                            System Uptime
                                        </span>
                                    </div>
                                    <span className="text-sm font-medium text-gray-900">
                                        99.9%
                                    </span>
                                </div>

                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-2 h-2 rounded-full bg-blue-500"></div>
                                        <span className="text-sm text-gray-600">
                                            Database
                                        </span>
                                    </div>
                                    <span className="text-sm font-medium text-gray-900">
                                        Healthy
                                    </span>
                                </div>

                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-2 h-2 rounded-full bg-purple-500"></div>
                                        <span className="text-sm text-gray-600">
                                            Version
                                        </span>
                                    </div>
                                    <span className="text-sm font-medium text-gray-900">
                                        v1.0.0
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
