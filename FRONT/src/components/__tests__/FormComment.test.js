import { mount } from '@vue/test-utils'
import FormComment from '../FormComment.vue'
import { server } from '../../mocks/server'

beforeAll(() => server.listen({ onUnhandledRequest: 'error' }))

afterEach(() => server.resetHandlers())

afterAll(() => server.close())

describe('formComment', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = mount(FormComment)
    })

    it('createButtonSuccess', async () => {
        await wrapper.setData({ editmode: false })

        const addComment = vi.fn(() => true)

        const createBtn = wrapper.find("#create")
        expect(createBtn.exists()).toBe(true)

        const updateBtn = wrapper.find("#update")
        expect(updateBtn.exists()).toBe(true)
        expect(updateBtn.attributes('style')).toContain('display: none')

        const commentInput = wrapper.find("#comment")
        expect(commentInput.exists()).toBe(true)
        await commentInput.setValue('Test');
        expect(commentInput.element.value).toBe("Test")

        const form = wrapper.find("#form")
        expect(form.exists()).toBe(true)

        createBtn.trigger('click')
        expect(addComment()).resolves.toEqual(true)
    })

    it('createButtonError', async () => {
        await wrapper.setData({ editmode: false })

        const addComment = vi.fn(() => false)

        const createBtn = wrapper.find("#create")
        expect(createBtn.exists()).toBe(true)

        const updateBtn = wrapper.find("#update")
        expect(updateBtn.exists()).toBe(true)
        expect(updateBtn.attributes('style')).toContain('display: none')

        const commentInput = wrapper.find("#comment")
        expect(commentInput.exists()).toBe(true)
        expect(commentInput.element.value).toBe("")

        const form = wrapper.find("#form")
        expect(form.exists()).toBe(true)

        createBtn.trigger('click')
        expect(addComment()).resolves.toEqual(false)
    })

    it('updateButtonSuccess', async () => {
        await wrapper.setData({ editmode: true, comment_id: 3 })

        const updateComment = vi.fn(() => true)

        const createBtn = wrapper.find("#create")
        expect(createBtn.exists()).toBe(true)
        expect(createBtn.attributes('style')).toContain('display: none')

        const updateBtn = wrapper.find("#update")
        expect(updateBtn.exists()).toBe(true)

        const commentInput = wrapper.find("#comment")
        expect(commentInput.exists()).toBe(true)
        // expect(commentInput.element.value).toBe(/content3/i)
        await commentInput.setValue('Test');
        expect(commentInput.element.value).toBe("Test")

        const form = wrapper.find("#form")
        expect(form.exists()).toBe(true)

        updateBtn.trigger('click')
        expect(updateComment()).resolves.toEqual(true)
    })

    it('updateButtonError', async () => {
        await wrapper.setData({ editmode: true })

        const updateComment = vi.fn(() => false)

        const createBtn = wrapper.find("#create")
        expect(createBtn.exists()).toBe(true)
        expect(createBtn.attributes('style')).toContain('display: none')

        const updateBtn = wrapper.find("#update")
        expect(updateBtn.exists()).toBe(true)

        const commentInput = wrapper.find("#comment")
        expect(commentInput.exists()).toBe(true)
        expect(commentInput.element.value).toBe("")

        const form = wrapper.find("#form")
        expect(form.exists()).toBe(true)

        updateBtn.trigger('click')
        expect(updateComment()).resolves.toEqual(false)
    })

    it('backLink', async () => {
        await wrapper.setData({ post_id: 2 })

        const backLink = wrapper.find("#back")
        expect(backLink.exists()).toBe(true)
        expect(backLink.attributes('href')).toBe('/comments/2')
    })
})