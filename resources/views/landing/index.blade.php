@extends('layouts.guest')

@section('content')
    <nav class="bg-blue-900 text-white">

        <div class="max-w-7xl mx-auto px-6">

            <div class="flex justify-between items-center h-20">

                <div>

                    <h1 class="font-bold text-xl">

                        KnowledgeRetain AI

                    </h1>

                    <p class="text-xs text-blue-200">

                        Sagay Water District

                    </p>

                </div>

                <div class="space-x-8 hidden md:flex">

                    <a href="#" class="hover:text-blue-300">Home</a>

                    <a href="#" class="hover:text-blue-300">About</a>

                    <a href="#" class="hover:text-blue-300">Features</a>

                    <a href="#" class="hover:text-blue-300">Contact</a>

                </div>

                <div class="space-x-3">

                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg bg-white text-blue-900 font-semibold">

                        Employee Login

                    </a>

                    <a href="#" class="px-5 py-2 rounded-lg bg-sky-500 hover:bg-sky-600">

                        Consumer Portal

                    </a>

                </div>

            </div>

        </div>

    </nav>
    <section
        class="bg-gradient-to-r
           from-blue-900
           via-blue-700
           to-sky-500
           text-white">

        <div class="max-w-7xl
               mx-auto
               px-8
               py-28">

            <div class="grid
                   lg:grid-cols-2
                   gap-12
                   items-center">

                <div>

                    <span
                        class="bg-white/20
                           px-4
                           py-2
                           rounded-full
                           text-sm">

                        Intelligent Maintenance System

                    </span>

                    <h1
                        class="text-6xl
                           font-bold
                           mt-6
                           leading-tight">

                        KnowledgeRetain AI

                    </h1>

                    <p class="mt-6
                           text-blue-100
                           text-lg">

                        Intelligent Maintenance Knowledge Management
                        System with AI-Based Repair Case Recommendation
                        for Sagay Water District.

                    </p>

                    <div class="mt-10 flex gap-4">

                        <a href="{{ route('login') }}"
                            class="bg-white
                               text-blue-900
                               px-7
                               py-4
                               rounded-xl
                               font-bold">

                            Employee Login

                        </a>

                        <a href="#"
                            class="bg-sky-500
                               px-7
                               py-4
                               rounded-xl
                               font-bold">

                            Consumer Portal

                        </a>

                    </div>

                </div>

                <div>

                    <div
                        class="bg-white
                           rounded-3xl
                           shadow-2xl
                           p-10">

                        <h2
                            class="text-3xl
                               font-bold
                               text-slate-800">

                            System Highlights

                        </h2>

                        <ul
                            class="mt-8
                               space-y-6
                               text-slate-700">

                            <li>
                                🤖 AI Repair Recommendation
                            </li>

                            <li>
                                📚 Knowledge Repository
                            </li>

                            <li>
                                📍 GIS Complaint Location
                            </li>

                            <li>
                                📋 Work Order Management
                            </li>

                            <li>
                                🔔 Maintenance Notifications
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </section>
@endsection
